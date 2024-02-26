<?php

declare(strict_types=1);


namespace App\Service;


use App\DTO\GroupDto;
use App\Enums\CommodityOperationType;

final readonly class GroupData
{

    /**
     * @param array<\App\DTO\CommodityDto> $currentCommodities
     */
    public function __construct(private array $currentCommodities)
    {
    }

    public function groupData(): GroupDto
    {
        $imports = [];
        $byProduct = [];
        $byOrder = [];
        $orderHistory = [];

        foreach ($this->currentCommodities as $commodity) {
            if ($commodity->getOperationType() == CommodityOperationType::IMPORT->name) {
                $imports[$commodity->getProductId()][] = $commodity;
            } else {
                $byProduct[$commodity->getProductId()][] = $commodity;
                $byOrder[$commodity->getRelatedOrderId()][] = $commodity;
                $orderHistory[$commodity->getRelatedOrderId()] = $commodity->getDeserializedHistory();
            }
        }

        return new GroupDto(imports: $imports, byProduct: $byProduct, byOrder: $byOrder, orderHistory: $orderHistory);
    }


}