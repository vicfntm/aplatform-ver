<?php

namespace App\Service;

use App\DTO\UpdateOrderDto;

interface OrderHandler
{

    /**
     * @param \App\DTO\UpdateOrderDto $updateOrderDto
     * @return array<\App\Entity\Commodity>
     */
    public function HandleOrderUpdate(UpdateOrderDto $updateOrderDto) : array;
}