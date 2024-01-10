<?php

declare(strict_types=1);


namespace App\Controller;


use App\DTO\OrderDto;
use App\Entity\Commodity;
use App\Entity\Delivery;
use App\Entity\Order;
use App\Enums\CommodityOperationType;
use App\Helper\SessionHelper;
use App\Service\RelevantCommodityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;

class OrderAction extends AbstractController
{

    public function __construct(
        private readonly RelevantCommodityService $commodityService,
        private readonly ValidatorInterface $validator,
        private readonly EntityManagerInterface $em,
        private readonly ObjectNormalizer $normalizer,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $i = $request->toArray();
        $dataDto = new OrderDto(products: $i['products'], delivery: $i['delivery']);
        $order = new Order();
        $order->setStatus(CommodityOperationType::PREORDER->name);
        $this->em->persist($order);

        foreach ($dataDto->getOrderProducts() as $product) {
            $childCommodities = $this->commodityService->defineCommodity($product);
            $errs = $this->validator->validate($childCommodities);
            if (count($errs) > 0) {
                $errorsString = (string)$errs;

                return new Response($errorsString);
            }
            /**
             * @var Commodity $childCommodity
             */
            foreach ($childCommodities->getCommodity() as $childCommodity) {
                $childCommodity->setOperationType(CommodityOperationType::PREORDER->name);
                $childCommodity->setRelatedOrder($order);
                $childCommodity->setProduct($childCommodity->getCommoditySource()->getProduct());
                $this->em->persist($childCommodity);
                $order->addCommodity($childCommodity);
            }
        }
        $delivery = new Delivery();
        $delivery->setCustomerOrder($order);
        $delivery->setAddress($dataDto->getAddress());
        $delivery->setTransportCompany($dataDto->getTransportType());
        $this->em->persist($delivery);
        $order->setDelivery($delivery);
        $normalizedOrder = $this->normalizer->normalize($order, null, ['groups' => 'public.order.read']);
        $order->setHistory($normalizedOrder);
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
                                              $order->getId()->toRfc4122()
                                          ),
                                      ], status: Response::HTTP_CREATED);
    }
}