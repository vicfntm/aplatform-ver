<?php

declare(strict_types=1);


namespace App\DTO;


use App\DTO\CommodityDto;
use App\Entity\Commodity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

final class OrderCommodityDto
{
    #[Assert\Positive]
    private int $stock;
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection<Commodity> $commodity
     */
    private ArrayCollection $commodity;

    public function __construct(ArrayCollection $commodity)
    {
        $this->commodity = $commodity;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): void
    {
        $this->stock = $stock;
    }

    public function getCommodity(): ArrayCollection
    {
        return $this->commodity;
    }







}