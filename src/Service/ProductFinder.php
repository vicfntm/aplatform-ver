<?php

declare(strict_types=1);


namespace App\Service;


use App\DTO\AggregationDto;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Uid\Ulid;

final readonly class ProductFinder implements Finder
{
    private readonly ObjectRepository $orderRepository;
    private readonly ObjectRepository $productRepository;
    public function __construct(private EntityManagerInterface $manager)
    {
        $this->orderRepository = $this->manager->getRepository(Order::class);
        $this->productRepository = $this->manager->getRepository(Product::class);
    }

    public function find(Ulid $ulid): iterable
    {
        $commodities = $this->orderRepository->findLastCommodities();
        $commodityPerCategory = $commodities->getByCategory()[$ulid->toRfc4122()];
        $arr = [];
        foreach ($commodityPerCategory as $k => $commoditySet) {
            $cmdSum = (new CommodityStockAnalyzer([$commoditySet]))->prepareCalculation()->sum();
            $arr[$k] = $cmdSum;
        }

        $productEntities = $this->productRepository->findProductSet(array_keys($arr));
        foreach ($productEntities as $product)
        {
            $aggregationDto = new AggregationDto(amount: $arr[$product->getId()->toRfc4122()]);
            $product->withAggregationParams($aggregationDto);
        }

        return $productEntities;
    }
}