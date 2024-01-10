<?php

declare(strict_types=1);


namespace App\Service;


use App\DTO\OrderCommodityDto;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use App\DTO\ProductDto;

class RelevantCommodityService
{

    private EntityRepository $productRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->productRepository = $entityManager->getRepository(Product::class);
    }

    public function defineCommodity(ProductDto $product): OrderCommodityDto
    {
        $parentCommodities = $this->productRepository->findCommoditySet($product->getId());

        return (new ChildCommodityCreator($parentCommodities, $product))->create();
    }
}