<?php

declare(strict_types=1);


namespace App\DTO;

final readonly class CommodityContainer
{

    public function __construct(private readonly array $commodities)
    {
    }

    public function getLastData(string $productId): array
    {
        $productCommodities = $this->commodities[$productId];

        return end($productCommodities);
    }


}