<?php

declare(strict_types=1);


namespace App\Controller;


use App\Convertors\OrderHistoryStorageConvertor;
use App\DTO\OrderDto;
use App\DTO\UpdateOrderDto;
use App\Entity\Commodity;
use App\Entity\Delivery;
use App\Entity\Order;
use App\Enums\CommodityOperationType;
use App\Helper\SessionHelper;
use App\Service\CFactory;
use App\Service\CommodityDefiner;
use App\Service\OrderHandler;
use App\Service\RelevantCommodityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;

class OrderAction extends AbstractController
{

    public function __construct(
//        private readonly CommodityDefiner $commodityService,
//        private readonly ValidatorInterface $validator,
        private readonly EntityManagerInterface $em,
        private readonly ObjectNormalizer $normalizer,
        private readonly OrderHandler $orderHandler,
        private readonly CFactory $commodityFactory,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $i = $request->toArray();
        ['transportType' => $transportType, 'address' => $address] = $i['delivery'];
        $order = new Order();
        $order->setStatus(CommodityOperationType::PREORDER->name);
        $this->em->persist($order);
        $this->em->flush();

        $dataDto = new UpdateOrderDto(
            products: $i['products'],
            orderId:  $order->getId()->toRfc4122(),
            status:   $order->getStatus()
        );
        try {
            $commodityDtoSet = $this->orderHandler->HandleOrderUpdate($dataDto);
            $commodities =  $this->commodityFactory->create($commodityDtoSet);

        } catch (\Throwable $exception) {
            return new JsonResponse($exception->getMessage(), $exception->getCode());
        }
        $delivery = new Delivery();
        $delivery->setCustomerOrder($order);
        $delivery->setAddress($address);
        $delivery->setTransportCompany($transportType);
        $this->em->persist($delivery);
        $order->setDelivery($delivery);
        array_walk($commodities, function(Commodity $commodity)use(&$order){
           $order->addCommodity($commodity);
        });
        $normalizedOrder = $this->normalizer->normalize($order, null, ['groups' => 'public.order.read']);
        $history = new OrderHistoryStorageConvertor($normalizedOrder);
        $order->setHistory($history->getHistory());
        $this->em->persist($order);
        $this->em->flush();
        // інтеграція з сесіями
        if (! $request->getSession()->getId()) {
            $request->getSession()->start();
        }
        /**
         * @var \Symfony\Component\HttpFoundation\Session\Session $session
         */
        $session = $request->getSession();
        $sessionHelper = new SessionHelper($session);
        $sessionHelper->pushOrder($order);

        // інтеграція з юзером

        return new JsonResponse(data: [
                                          'permalink' => sprintf(
                                              '%s/%s',
                                              '/api/public/orders',
                                              $order->getId()
                                          ),
                                      ], status: Response::HTTP_CREATED);
    }
}