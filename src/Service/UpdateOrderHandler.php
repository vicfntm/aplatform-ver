<?php

declare(strict_types=1);


namespace App\Service;


use App\DTO\UpdateOrderDto;
use App\Entity\Order;
use App\Helper\CommodityCollectionClearer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class UpdateOrderHandler implements OrderHandler
{

    private EntityRepository $orderRepo;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->orderRepo = $this->entityManager->getRepository(Order::class);
    }

    /**
     * @throws \App\Exception\NotEnoughStockException
     */
    public function HandleOrderUpdate(UpdateOrderDto $updateOrderDto): array
    {
        $operations = $this->orderRepo->findLastCommodities();
        $byProduct = new CommodityCollectionClearer(commodities: $operations->getByProduct());
        $cmd = [];
        foreach ($updateOrderDto->getOrderProducts() as $product) {
            $noCurrentCommoditySet = $byProduct->eliminateEntityById(
                $updateOrderDto->getOrderIdConvertedToRfc(),
                $product
            );
            $productImports = $operations->getImports()[$product->getIdAsRfc()];
            $generator = new CommodityGenerator(
                productToUpdate: $product,
                transactions:    $noCurrentCommoditySet,
                imports:         $productImports,
                orderId:         $updateOrderDto->getOrderIdConvertedToRfc(),
                status:          $updateOrderDto->getStatus(),
            );
            $cmd = array_merge($cmd, $generator->generateCommodities());
        }

        return $cmd;
    }
}
