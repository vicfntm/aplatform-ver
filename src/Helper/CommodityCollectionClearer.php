<?php

declare(strict_types=1);


namespace App\Helper;


use App\DTO\CommodityDto;
use App\DTO\ProductDto;
use Symfony\Component\Uid\Ulid;

final readonly class CommodityCollectionClearer
{

    /**
     * @param array<\App\DTO\CommodityDto> $commodities
     */
    public function __construct(private array $commodities)
    {

    }

    /**
     * @param string $excludeId
     * @param \App\DTO\ProductDto $productDto
     * @return array<CommodityDto>
     */
    public function eliminateEntityById( string $excludeId, ProductDto $productDto) : array
    {
        /**
         * @var array<CommodityDto> $commodities
         */
        $commodities = $this->commodities[(new Ulid($productDto->getId()))->toRfc4122()];
        return array_filter($commodities, function(CommodityDto $cmd) use($excludeId){
            return   $cmd->getRelatedOrderId() != $excludeId;
        });

    }
}