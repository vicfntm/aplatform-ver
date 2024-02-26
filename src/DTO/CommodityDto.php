<?php

declare(strict_types=1);


namespace App\DTO;


use Symfony\Component\Uid\Ulid;

final readonly class CommodityDto
{

    public function __construct(
        private ?string $relatedOrderId,
        private ?string $commoditySourceId,
        private string $operationTimestamp,
        private int $amount,
        private string $operationType,
        private string $id,
        private string $productId,
        private ?string $history,

    ) {
    }

    public function getRelatedOrderId(): ?string
    {
        return $this->relatedOrderId;
    }

    public function getCommoditySourceId(): ?string
    {
        return $this->commoditySourceId;
    }

    public function getOperationTimestamp(): string
    {
        return $this->operationTimestamp;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getOperationType(): string
    {
        return $this->operationType;
    }

    public function getId(): string
    {
        return $this->id;
    }
    public function getProductIdConvertedToBase32() : string
    {
        return Ulid::fromString($this->getProductId())->toBase32();
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getHistory(): string
    {
        return $this->history;
    }

    public function getDeserializedHistory(): array
    {
        return is_null($this->history) ? [] : json_decode($this->history, associative: true);
    }

}
