<?php

declare(strict_types=1);


namespace App\Service;


use App\Entity\Commodity;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Uid\Ulid;

readonly class CommodityFactory implements CFactory
{

    private ObjectRepository $orderRepo;
    private ObjectRepository $commodityRepo;
    private ObjectRepository $productRepo;

    public function __construct(private EntityManagerInterface $entityManager)
    {
        $this->orderRepo = $this->entityManager->getRepository(Order::class);
        $this->commodityRepo = $this->entityManager->getRepository(Commodity::class);
        $this->productRepo = $this->entityManager->getRepository(Product::class);
    }

    /**
     * @param iterable<\App\DTO\CommodityDto> $data
     * @return array|\App\Entity\Commodity[]
     */
    public function create(iterable $data): array
    {
        $entities =  $this->HydrateEntities($data);
        foreach ($entities as $entity) {

            $this->entityManager->persist($entity);
        }
//        $this->entityManager->flush();

        return $entities;
    }

    private function HydrateEntities(iterable $data): array
    {
        $opTimestamp = new Ulid();
        $res = [];
//        $orderEntity = null;
//        $orderId = null;
        foreach ($data as $commodityDto) {
//            $orderId = $orderId ?? $commodityDto->getRelatedOrderId();
//            $orderEntity = $orderEntity ?? $this->orderRepo->findOneBy(['id' => $orderId]);
            $commoditySource = $this->commodityRepo->findOneBy(['id' => $commodityDto->getCommoditySourceId()]);
            $productEntity = $this->productRepo->findOneBy(['id' => $commodityDto->getProductId()]);
            $commodity = new Commodity();
            $commodity->setAmount($commodityDto->getAmount());
            $commodity->setProduct($productEntity);
//            $commodity->setRelatedOrder($orderEntity);
            $commodity->setCommoditySource($commoditySource);
            $commodity->setOperationType($commodityDto->getOperationType());
            $commodity->setOperationTimestamp($opTimestamp);
//            $orderEntity->addCommodity($commodity);
            $res[] = $commodity;
        }

        return $res;
    }
}