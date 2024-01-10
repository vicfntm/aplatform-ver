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
     * @param \Doctrine\ORM\PersistentCollection<\App\Entity\Commodity> $commodities
     * @return \App\DTO\OrderCommodityDto
     */
    public function aggregate(PersistentCollection $commodities): OrderCommodityDto
    {
        /** @var \App\Entity\Commodity $commodity
         *
         */
        foreach ($commodities as $commodity){
            $status = $commodity->getOperationType();
            if($status === CommodityOperationType::IMPORT->name){
                $targetId = $commodity->getId()->toBase32();
                $amt = $commodity->getAmount();
                $this->registry[$targetId] = ['cmd' => $commodity, 'amt' => $amt];
            }else{
                $targetId = $commodity->getCommoditySource()->getId()->toBase32();
                $amt = $commodity->getAmount();
                ['cmd' => $commodity, 'amt' => $a] = $this->registry[$targetId];
                $a -= $amt;
                $this->registry[$targetId] = ['cmd' => $commodity, 'amt' => $a];
                if($a <= 0){
                    unset($this->registry[$targetId]);
                }
            }
        }
        if(empty($this->registry)){
            throw new \RuntimeException('luck of stock exception');
        };
        $oc = new OrderCommodityDto(new ArrayCollection($this->registry));
        $oc->setStock(array_sum(array_column($this->registry, 'amt')));
        return $oc;
    }
//    protected $children;
//
//    public function __construct()
//    {
//        $this->children = new \SplObjectStorage();
//    }
//
//    function add(Composite $composite): void
//    {
//        $this->children->attach($composite);
//    }
//
//    function remove(Composite $composite): void
//    {
//        $this->children->detach($composite);
//        $composite->setParent(null);
//    }
//
//    public function calculate()
//    {
//        $w = null;
//    }
}