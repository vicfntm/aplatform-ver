<?php

namespace App\Repository;

use App\DTO\GroupDto;

interface LastCommoditySet
{
    public function findLastCommodities(): GroupDto;
}