<?php

declare(strict_types=1);


namespace App\Convertors;


use App\Convertors\CommodityComposite;
use App\DTO\CommodityDto;
use App\Enums\CommodityOperationType;

class CommodityStorage extends CommodityComposite
{
    private ?string $importId;

    public function __construct(private readonly string $type, private readonly int $amount, private readonly string $id, private readonly bool|int $price)
    {
    }

    public function operate(): array
    {
        return ['type' => $this->type, 'amount' => $this->amount, 'id' => $this->id, 'price' => $this->price];
    }


}
