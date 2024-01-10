<?php

declare(strict_types=1);


namespace App\Service;


use App\DTO\OrderCommodityDto;
use App\DTO\ProductDto;
use App\Entity\Commodity;
use Doctrine\Common\Collections\ArrayCollection;

class ChildCommodityCreator
{

    private ArrayCollection $childCommodities;

    public function __construct(
        private readonly OrderCommodityDto $parentCommodities,
        private readonly ProductDto $orderProduct
    ) {
        $this->childCommodities = new ArrayCollection();
    }

    public function create(): OrderCommodityDto
    {
        $childAmtCounter = $this->orderProduct->getAmount();
        if ($childAmtCounter > $this->parentCommodities->getStock()) {
            throw new \RuntimeException('not enough stock for this order request');
        }
        /**
         * @var ArrayCollection<\App\Entity\Commodity> $parentCommodities
         */
        $parentCommodities = $this->parentCommodities->getCommodity();

        do {
            ['cmd' => $parentCommodity, 'amt' => $parentCommodityAmountAvailable] = $parentCommodities->current();
            $commodityQtyFromCurrentCommodity = ($childAmtCounter >= $parentCommodityAmountAvailable) ? $parentCommodityAmountAvailable : $childAmtCounter;
            $child = new Commodity();
            $child->setAmount($commodityQtyFromCurrentCommodity);
            $child->setCommoditySource($parentCommodity);
            $this->childCommodities->add($child);
            $childAmtCounter -= $commodityQtyFromCurrentCommodity;
            $parentCommodities->next();
        } while ($childAmtCounter);

        return new OrderCommodityDto($this->childCommodities);
    }
}