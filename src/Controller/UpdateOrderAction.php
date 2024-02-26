<?php

declare(strict_types=1);


namespace App\Controller;


use App\Entity\Order;
use App\Service\OrderFactory;
use App\Service\Persister;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\DTO\UpdateOrderDto;
use App\Service\OrderHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Service\CFactory;

class UpdateOrderAction extends AbstractController
{

    public function __construct(
        private readonly OrderHandler $commodityHandler,
        private readonly ValidatorInterface $validator,
        private readonly CFactory $commodityFactory,
        private readonly OrderFactory $orderFactory,
        private readonly Persister $persister,
    ) {
    }

    public function __invoke(Request $request, string $id): JsonResponse
    {
        $i = $request->toArray();
        $dataDto = new UpdateOrderDto(products: $i['products'], orderId: $id, status: $i['status']);
        try {
            $commodityDtoSet = $this->commodityHandler->HandleOrderUpdate($dataDto);
            $commodities = $this->commodityFactory->create($commodityDtoSet);
            $order = $this->orderFactory->create($commodities, $id);
            $this->persister->persistEntity($order);
        } catch (\Throwable $exception) {
            return new JsonResponse($exception->getMessage(), $exception->getCode());
        }

        // todo handle delivery

        return new JsonResponse(['updatedOrderId' => $order->getId()->toBase32()]);



    }
}