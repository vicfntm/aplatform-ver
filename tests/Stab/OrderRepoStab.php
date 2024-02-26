<?php

declare(strict_types=1);


namespace App\Tests\Stab;


use App\DTO\CommodityDto;
use App\Enums\CommodityOperationType;
use App\Repository\LastCommoditySet;
use App\Service\GroupData;
use Symfony\Component\Uid\Ulid;

class OrderRepoStab implements LastCommoditySet
{

    private array $data = [];

    public function __construct(private readonly string $datasetName = 'default')
    {
        $orderId = (new Ulid())->toRfc4122();
        $secondOrderId = (new Ulid())->toRfc4122();
        $thirdOrderId = (new Ulid())->toRfc4122();
        $productId = (new Ulid())->toRfc4122();
        $secondProductId = (new Ulid())->toRfc4122();
        $importId = (new Ulid())->toRfc4122();
        $secondImportId = (new Ulid())->toRfc4122();
        $testDataSet = [
            'default'                        => [
                [
                    '',
                    '',
                    (new Ulid())->toRfc4122(),
                    100,
                    CommodityOperationType::IMPORT->name,
                    $importId,
                    $productId,
                    '[]',
                ],
                [
                    $orderId,
                    '',
                    (new Ulid())->toRfc4122(),
                    5,
                    CommodityOperationType::PREORDER->name,
                    $importId,
                    $productId,
                    '[]',
                ],
                [
                    $secondOrderId,
                    '',
                    (new Ulid())->toRfc4122(),
                    8,
                    CommodityOperationType::CORRECTION->name,
                    $importId,
                    $productId,
                    '[]',
                ],
                [
                    $thirdOrderId,
                    '',
                    (new Ulid())->toRfc4122(),
                    2,
                    CommodityOperationType::PREORDER->name,
                    $importId,
                    $productId,
                    '[]',
                ],
            ],
            'twoImports'                     => [
                [
                    '',
                    '',
                    (new Ulid())->toRfc4122(),
                    100,
                    CommodityOperationType::IMPORT->name,
                    $importId,
                    $productId,
                    '[]',
                ],
                [
                    '',
                    '',
                    (new Ulid())->toRfc4122(),
                    50,
                    CommodityOperationType::IMPORT->name,
                    $secondImportId,
                    $productId,
                    '[]',
                ],
                [
                    $orderId,
                    $importId,
                    (new Ulid())->toRfc4122(),
                    1,
                    CommodityOperationType::CORRECTION->name,
                    (new Ulid())->toRfc4122(),
                    $productId,
                    '[]',
                ],
                [
                    $secondOrderId,
                    $importId,
                    (new Ulid())->toRfc4122(),
                    2,
                    CommodityOperationType::CORRECTION->name,
                    (new Ulid())->toRfc4122(),
                    $productId,
                    '[]',
                ],
                [
                    $thirdOrderId,
                    $secondImportId,
                    (new Ulid())->toRfc4122(),
                    7,
                    CommodityOperationType::CORRECTION->name,
                    (new Ulid())->toRfc4122(),
                    $productId,
                    '[]',
                ],

            ],
            'generatorSet'                   => [
                [
                    '',
                    '',
                    (new Ulid())->toRfc4122(),
                    100,
                    CommodityOperationType::IMPORT->name,
                    $importId,
                    $productId,
                    '[]',
                ],
                [
                    $orderId,
                    $importId,
                    (new Ulid())->toRfc4122(),
                    1,
                    CommodityOperationType::CORRECTION->name,
                    (new Ulid())->toRfc4122(),
                    $productId,
                    '[]',
                ],
                [
                    $secondOrderId,
                    $importId,
                    (new Ulid())->toRfc4122(),
                    2,
                    CommodityOperationType::CORRECTION->name,
                    (new Ulid())->toRfc4122(),
                    $productId,
                    '[]',
                ],
                [
                    $thirdOrderId,
                    $secondImportId,
                    (new Ulid())->toRfc4122(),
                    7,
                    CommodityOperationType::CORRECTION->name,
                    (new Ulid())->toRfc4122(),
                    $productId,
                    '[]',
                ],
            ],
            'noImportContainReturnStatusSet' => [
                [
                    $secondOrderId,
                    $importId,
                    (new Ulid())->toRfc4122(),
                    2,
                    CommodityOperationType::CORRECTION->name,
                    (new Ulid())->toRfc4122(),
                    $productId,
                    '[]',
                ],
                [
                    $thirdOrderId,
                    $importId,
                    (new Ulid())->toRfc4122(),
                    7,
                    CommodityOperationType::RETURN->name,
                    (new Ulid())->toRfc4122(),
                    $productId,
                    '[]',
                ],


            ],
            'doubleImport'                   => [
                [
                    '',
                    '',
                    (new Ulid())->toRfc4122(),
                    5,
                    CommodityOperationType::IMPORT->name,
                    $importId,
                    $productId,
                    '[]',
                ],
                [
                    '',
                    '',
                    (new Ulid())->toRfc4122(),
                    10,
                    CommodityOperationType::IMPORT->name,
                    $importId,
                    $productId,
                    '[]',
                ],
                [
                    $thirdOrderId,
                    $importId,
                    (new Ulid())->toRfc4122(),
                    1,
                    CommodityOperationType::CORRECTION->name,
                    (new Ulid())->toRfc4122(),
                    $productId,
                    '[]',
                ],
            ],
            'twoProducts' => [
                [
                    '',
                    '',
                    (new Ulid())->toRfc4122(),
                    5,
                    CommodityOperationType::IMPORT->name,
                    $importId,
                    $productId,
                    '[]',
                ],
                [
                    '',
                    '',
                    (new Ulid())->toRfc4122(),
                    10,
                    CommodityOperationType::IMPORT->name,
                    $secondImportId,
                    $secondProductId,
                    '[]',
                ],
                [
                    $orderId,
                    $importId,
                    (new Ulid())->toRfc4122(),
                    1,
                    CommodityOperationType::PREORDER->name,
                    (new Ulid())->toRfc4122(),
                    $productId,
                    '[]',
                ],
                [
                    $orderId,
                    $secondImportId,
                    (new Ulid())->toRfc4122(),
                    1,
                    CommodityOperationType::PREORDER->name,
                    (new Ulid())->toRfc4122(),
                    $secondProductId,
                    '[]',
                ],

            ]
        ];
        $this->data = $testDataSet;
    }


    /**
     * @return \App\DTO\GroupDto
     */
    public function findLastCommodities(): \App\DTO\GroupDto
    {
        $aggregation = new GroupData(array_map(function ($e) {
            return new CommodityDto(
                relatedOrderId:     $e[0],
                commoditySourceId:  $e[1],
                operationTimestamp: $e[2],
                amount:             $e[3],
                operationType:      $e[4],
                id:                 $e[5],
                productId:          $e[6],
                history:            $e[7]
            );
        }, $this->data[$this->datasetName]));

        return $aggregation->groupData();
    }
}