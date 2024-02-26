<?php

namespace App\Tests;

use App\Service\CommodityStockAnalyzer;
use App\Service\GroupData;
use App\Tests\Stab\OrderRepoStab;
use PHPUnit\Framework\TestCase;

class CommodityStockCalculatorTest extends TestCase
{
    public function testStockCalculator(): void
    {
        $repo = new OrderRepoStab();
        $groupData = $repo->findLastCommodities();
        $calculator = new CommodityStockAnalyzer($groupData->getByProduct());
        $res = $calculator->prepareCalculation()->sum();
        $this->assertTrue($res == -15);
    }

    public function testStockForTwoImports()
    {
        $repo = new OrderRepoStab(datasetName: 'twoImports');
        $groupData = $repo->findLastCommodities();;
        $calculator = new CommodityStockAnalyzer($groupData->getByProduct());
        $res = $calculator->prepareCalculation()->sum();
        $this->assertTrue($res == -10);

    }
}
