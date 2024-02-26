<?php

declare(strict_types=1);


namespace App\Service;


use App\Convertors\OrderHistoryStorageConvertor;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

readonly class OrderUpdater implements OrderFactory
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ObjectNormalizer $normalizer

    ) {
    }

    /**
     * @param iterable<\App\Entity\Commodity> $commodities
     * @param string $orderId
     * @return \App\Entity\Order
     */
    public function create(iterable $commodities, string $orderId): Order
    {
        $orderRepo = $this->entityManager->getRepository(Order::class);

        $orderEntity = $orderRepo->findOneBy(['id' => $orderId]);
        $prev = (new OrderHistoryStorageConvertor(
            $this->normalizer->normalize($orderEntity, null, ['groups' => 'public.order.read'])
        ))->getHistory();
        foreach ($commodities as $commodity) {
            $orderEntity->addCommodity($commodity);
            $commodity->setRelatedOrder($orderEntity);
        }
        $curr = (new OrderHistoryStorageConvertor(
            $this->normalizer->normalize($orderEntity, null, ['groups' => 'public.order.read'])
        ))->getHistory();

        $orderEntity->setHistory(array_merge($prev, $curr));
//        $this->entityManager->persist($orderEntity);
//        $this->entityManager->flush();
        return $orderEntity;
    }
}