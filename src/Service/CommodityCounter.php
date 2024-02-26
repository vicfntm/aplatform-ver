<?php

declare(strict_types=1);


namespace App\Service;


use App\DTO\OrderCommodityDto;
use App\Entity\Commodity;
use App\Enums\CommodityOperationType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

class CommodityCounter
{

    private array $registry = [];

    /**
     * @param iterable<\App\Entity\Commodity> $commodities
     * @return \App\DTO\OrderCommodityDto
     */
    public function aggregate(iterable $commodities): OrderCommodityDto
    {
        $this->groupData($commodities);
        $oc = new OrderCommodityDto(new ArrayCollection($this->registry));
        $oc->setStock(array_sum(array_column($this->registry, 'amt')));

        return $oc;
    }

    private function groupData(iterable $commodities): void
    {
        $nonImportCollection = [];
        foreach ($commodities as $commodity) {
            $operationTime = $commodity->getOperationTimestamp()->toBase32();
            $amt = $commodity->getAmount();
            try {
                if ($commodity->getOperationType() !== CommodityOperationType::IMPORT->name) {
                    $nonImportCollection[$operationTime][] = ['cmd' => $commodity, 'amt' => -$amt];
                } else {
                    $this->registry[$operationTime] = ['cmd' => $commodity, 'amt' => $amt];
                }
            } catch (\Error $exception) {
                $w = null;
            }
        }
        if (! empty($nonImportCollection)) {
            $lastCustomerOrderUpdate = end($nonImportCollection)[0];
            $operationTime = key($nonImportCollection);
            $this->registry[$operationTime] = $lastCustomerOrderUpdate;
        }

        if (empty($this->registry)) {
            throw new \RuntimeException('luck of stock exception');
        }
    }

    public function withLastCommoditySet(iterable $commodities): array
    {
        $this->groupData($commodities);

        return $this->registry;
    }
}