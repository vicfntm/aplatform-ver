<?php

namespace App\Service;

use App\Entity\Commodity;
use App\Entity\Order;

interface CFactory
{

    /**
     * @param iterable<\App\DTO\CommodityDto> $data
     * @return array<Commodity>
     */
    public function create(iterable $data) : array;
}