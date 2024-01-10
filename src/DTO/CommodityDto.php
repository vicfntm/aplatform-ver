<?php

declare(strict_types=1);


namespace App\DTO;


use App\Entity\Commodity;
use App\Enums\CommodityOperationType;

class CommodityDto
{
    private int $importAmount;
    private string $importId;
    /** @var Commodity[] $commodities */
    private array $commodities;

    public function __construct(?Commodity $commodity)
    {
        if ($commodity->getOperationType() === CommodityOperationType::IMPORT->name) {
            $this->importAmount = $commodity->getPrice()->last()->getPrice();
            $this->importId = $commodity->getId()->toBase32();

        }
    }

}
