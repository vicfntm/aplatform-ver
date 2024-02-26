<?php

namespace App\Tests;

use App\Service\CommodityStockAnalyzer;
use App\Tests\Stab\OrderRepoStab;
use PHPUnit\Framework\TestCase;

class CommodityStockAnalyzerTest extends TestCase
{

    const EXPECTED = [
        'SOLD_PER_PRODUCT'       => [
            'generatorSet' => 90,
            'default'      => 85,
            'twoImports'   => 140,
        ],
        'correctTransactionSign' => [
            'generatorSet'                   => -10,
            'noImportContainReturnStatusSet' => 5,
        ],
    ];

    public function testCorrectTransactionSignLogic(): void
    {
        foreach (self::EXPECTED['correctTransactionSign'] as $databaseName => $expected) {
            $repoData = (new OrderRepoStab($databaseName))->findLastCommodities();
            $analyzer = new CommodityStockAnalyzer(transactionsByProduct: $repoData->getByProduct());

            $this->assertEquals($expected, $analyzer->prepareCalculation()->sum());
        }
    }

    public function testCalculateGeneralSoldPerProduct()
    {
        foreach (self::EXPECTED['SOLD_PER_PRODUCT'] as $datasetName => $expected) {
            $repoData = (new OrderRepoStab($datasetName))->findLastCommodities();
            $byProduct = $repoData->getByProduct();
            $productId = key($byProduct);
            $productImports = $repoData->getImports()[$productId];
            $analyzer = new CommodityStockAnalyzer(transactionsByProduct: array_merge($byProduct, [$productImports]));
            $sum = $analyzer->prepareCalculation()->sum();

            $this->assertEquals($expected, $sum);
        }
    }


}
