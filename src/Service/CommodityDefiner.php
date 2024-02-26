<?php

namespace App\Service;

use App\DTO\OrderCommodityDto;
use App\DTO\ProductDto;

interface CommodityDefiner
{
    public function defineCommodity(ProductDto $product) : OrderCommodityDto;
}