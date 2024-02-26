<?php

declare(strict_types=1);


namespace App\DTO;


final readonly class GroupDto
{

    private array $soldPerCommodities;

    /**
     * @param array<string, \App\DTO\CommodityDto> $imports
     * @param array $byProduct
     * @param array $byOrder
     * @param array $orderHistory
     */
    public function __construct(
        private array $imports = [],
        private array $byProduct = [],
        private array $byOrder = [],
        private array $orderHistory = []
    ) {
    }


    public function getImports(): array
    {
        return $this->imports;
    }

    public function getByProduct(): array
    {
        return $this->byProduct;
    }

    public function getByOrder(): array
    {
        return $this->byOrder;
    }

    public function setDebt(array $data): void
    {
        $this->soldPerCommodities = $data;
    }

    public function getDebt(): array
    {
        return $this->soldPerCommodities;
    }

    public function getOrderHistory(): array
    {
        return $this->orderHistory;
    }

    private function getLatestCommoditiesForOrder(string $orderId)
    {
        $history = $this->orderHistory;

        return end($history[$orderId])['commodities'];
    }

    public function findLatestCommodityPerOrderProduct(string $orderId, string $productId) : array
    {
        $latestHistory = $this->getLatestCommoditiesForOrder($orderId);
        return array_filter($latestHistory, function ($e) use ($productId) {
            return $e['product']['id'] == $productId;
        });
    }


    /**
     * @param string $orderId
     * @param string $productId
     * @return array<\App\DTO\CommodityDto>
     */
    public function getCommoditiesByOrderProduct(string $orderId, string $productId) : array
    {
        $orderCommodities = $this->getByOrder()[$orderId];
        return array_filter($orderCommodities, function ($cmd) use($productId){
            return $cmd->getProductIdConvertedToBase32() === $productId;
        });
    }


}