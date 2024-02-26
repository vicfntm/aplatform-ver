<?php

namespace App\Tests;

use App\DTO\CommodityDto;
use App\DTO\UpdateOrderDto;
use App\Entity\Commodity;
use App\Exception\NotEnoughStockException;
use App\Helper\CommodityCollectionClearer;
use App\Service\CommodityGenerator;
use App\Tests\Stab\OrderRepoStab;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Ulid;


class CommodityGeneratorTest extends TestCase
{

    private const EXPECTED = [
        [
            'datasetName' => 'generatorSet',
            'cases'       => [
                ['input' => 2, 'result' => 2],
                ['input' => 3, 'result' => 3],
                ['input' => 10, 'result' => 10],
                ['input' => 12, 'result' => 12],
            ],
        ],
        [
            'datasetName' => 'doubleImport',
            'cases'       => [
                ['input' => 2, 'result' => 2],
                ['input' => 5, 'result' => 5],
                ['input' => 15, 'result' => 15],
            ],
        ],

    ];

    private const NOT_ENOUGH_EXCEPTION = [
        [
            'datasetName' => 'default',
            'cases'       => [
                100,
                200,
                10000,
            ],
        ],
    ];

    private const TWO_PRODUCTS_REQUEST = [
        'twoProducts' => [
            'datasetName' => 'twoProducts',
            'cases'       => [
                [1, 5],
                [2, 7],
                [3, 8],
                [4, 4],
            ],
        ],
    ];


    /**
     * @throws \App\Exception\NotEnoughStockException
     */
    public function testCommodityGenerator()
    {
        foreach (self::EXPECTED as $case) {
            foreach ($case['cases'] as $params) {
                ['input' => $input, 'result' => $result] = $params;

                $this->runCase($case['datasetName'], $input, $result);
            }
        }
    }

    /**
     * @throws \App\Exception\NotEnoughStockException
     */
    private function runCase(string $dataset, int $input, int $expectancy): void
    {
        $repoData = (new OrderRepoStab($dataset))->findLastCommodities();
        $orderId = key($repoData->getByOrder());
        /**
         * @var array<\App\DTO\CommodityDto> $productSet
         */
        $commoditiesByOrder = $repoData->getByOrder()[$orderId];
        $productId = (end($commoditiesByOrder))->getProductId();
        $productIdApiForm = (Ulid::fromString($productId))->toBase32();
        $byProduct = new CommodityCollectionClearer(commodities: $repoData->getByProduct()); // repository fake
        $updateOrderDto = new UpdateOrderDto(products: [['product' => $productIdApiForm, 'amount' => $input]],
            orderId:                                   $orderId,
            status:                                    'CORRECTION'); // incoming data
        $commoditySet = [];
        foreach ($updateOrderDto->getOrderProducts() as $product) {
            $noCurrentCommoditySet = $byProduct->eliminateEntityById(
                $orderId,
                $product
            );  // clear current transaction order data - this is single "DB" source for code below
            // define correct sign based commodity status
            /**
             * @var array<\App\DTO\CommodityDto> $imports
             */
            $imports = $repoData->getImports()[$product->getIdAsRfc()];
            $generator = new CommodityGenerator(
                productToUpdate: $product,
                transactions:    $noCurrentCommoditySet,
                imports:         $imports,
                orderId:         $orderId,
            );
            $commodities = $generator->generateCommodities();
            $commoditySet = array_merge($commoditySet, $commodities);
        }
        $commodityAmount = array_map(function (CommodityDto $cmd) {
            return $cmd->getAmount();
        }, $commoditySet);
        $this->assertEquals($expectancy, array_sum($commodityAmount));
    }

    /**
     */
    public function testNotEnoughException()
    {
        foreach (self::NOT_ENOUGH_EXCEPTION as $e) {
            $res = '';
            foreach ($e['cases'] as $case) {
                try {
                    $this->runCase($e['datasetName'], $case, $case);
                } catch (\Exception $exception) {
                    $res = $exception;
                }
                $this->assertTrue($res instanceof NotEnoughStockException);
            }
        }
    }

    /**
     * @throws \App\Exception\NotEnoughStockException
     */
    public function testTwoProducts()
    {
        $params = self::TWO_PRODUCTS_REQUEST;
        $repoData = (new OrderRepoStab($params['twoProducts']['datasetName']))->findLastCommodities();
        $orderId = key($repoData->getByOrder());
        /**
         * @var array<\App\DTO\CommodityDto> $productSet
         */
        $commoditiesByOrder = $repoData->getByOrder()[$orderId];
        $productId = $commoditiesByOrder[0]->getProductId();
        $productId2 = $commoditiesByOrder[1]->getProductId();
        $productIdApiForm = (Ulid::fromString($productId))->toBase32();
        $productIdApiForm2 = (Ulid::fromString($productId2))->toBase32();
        $byProduct = new CommodityCollectionClearer(commodities: $repoData->getByProduct()); // repository fake
        foreach ($params['twoProducts']['cases'] as $case) {
            [$product1Amt, $product2Amt] = $case;
            $updateOrderDto = new UpdateOrderDto(
                products: [
                              ['product' => $productIdApiForm, 'amount' => $product1Amt],
                              ['product' => $productIdApiForm2, 'amount' => $product2Amt],
                          ],
                orderId:  $orderId,
                status:   'CORRECTION'
            ); // incoming data
            $commoditySet = [];
            foreach ($updateOrderDto->getOrderProducts() as $index => $product) {
                $noCurrentCommoditySet = $byProduct->eliminateEntityById(
                    $orderId,
                    $product
                );  // clear current transaction order data - this is single "DB" source for code below
                // define correct sign based commodity status
                /**
                 * @var array<\App\DTO\CommodityDto> $imports
                 */
                $imports = $repoData->getImports()[$product->getIdAsRfc()];
                $generator = new CommodityGenerator(
                    productToUpdate: $product,
                    transactions:    $noCurrentCommoditySet,
                    imports:         $imports,
                    orderId:         $orderId,
                );
                $commodities = $generator->generateCommodities();

                $commodityAmount = array_map(function (CommodityDto $cmd) {
                    return $cmd->getAmount();
                }, $commodities);
                $this->assertEquals($case[$index], array_sum($commodityAmount));
            }
        }
    }
}
