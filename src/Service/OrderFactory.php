<?php

namespace App\Service;

use App\Entity\Order;

interface OrderFactory
{

    /**
     * @param iterable<\App\Entity\Commodity> $commodities
     * @param string $orderId
     * @return \App\Entity\Order
     */
    public function create(iterable $commodities, string $orderId) : Order;
}