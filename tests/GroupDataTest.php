<?php

namespace App\Tests;

use App\DTO\GroupDto;
use App\Service\GroupData;
use App\Tests\Stab\OrderRepoStab;
use PHPUnit\Framework\TestCase;

class GroupDataTest extends TestCase
{
    public function testCheckInstance(): void
    {
        $repo = new OrderRepoStab();
        $groupData = $repo->findLastCommodities();
        $this->assertInstanceOf(GroupDto::class, $groupData);

    }

    public function testCheckCountForImport() : void
    {
        $repo = new OrderRepoStab();
        $groupData = $repo->findLastCommodities();
        $this->assertCount(1, $groupData->getImports());

    }

    public function testCheckCountForProductCommodities() : void
    {
        $repo = new OrderRepoStab();
        $groupData = $repo->findLastCommodities();
        $this->assertCount(1, $groupData->getByProduct());
    }
}
