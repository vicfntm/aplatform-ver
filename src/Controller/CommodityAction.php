<?php

declare(strict_types=1);


namespace App\Controller;


use App\Entity\Commodity;
use App\Entity\Order;
use App\Entity\Price;
use App\Entity\Product;
use App\Entity\Views;
use App\Enums\CommodityOperationType;
use App\Helper\SessionHelper;
use App\Helper\UriParser;
use App\Repository\CommodityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CommodityAction extends AbstractController
{
    private const STATUS = 'SALE';
    private CommodityRepository|\Doctrine\ORM\EntityRepository $commodityRepo;

    public function __construct(private readonly EntityManagerInterface $em)
    {
        $this->productRepo = $this->em->getRepository(Product::class);
    }

    public function __invoke(Request $request): object
    {
        $data = $request->toArray();
//        $order = new Order();
//        foreach ($data as $i) {

        /** метод записує статистику по переглядам до бази даних
         * привязує айді сесії до цих переглядів
         **/
        if (!$request->getSession()->getId()) {
            $request->getSession()->start();
        }
//            $request->getSession()->isStarted() || $request->getSession()->start();
        $sessionId = $request->getSession()->getId();
        foreach ($data as $product) {
            $product = $this->productRepo->findOneBy(['id' => $product['product']]);
            $view    = new Views();
            $view->setSessionId($sessionId);
            $view->setProduct($product);
            $this->em->persist($view);
        }
        $this->em->flush();


        return new class() {
        };

//            $parentCommodity = $this->commodityRepo->findCommodityByRawId($i['CommoditySource']);
//            $lastParentPrice = $parentCommodity->getPrice()->last();
//            $commodityCurrentPrice = new Price();
//            $commodityCurrentPrice->setPrice($lastParentPrice->getPrice());
//            $c         = new Commodity();
//            $c->setProduct($parentCommodity->getProduct());
//            $c->setCommoditySource($parentCommodity);
//            $c->setAmount($i['amount']);
//            $c->setOperationType(CommodityOperationType::PREORDER->name);
//            $c->addPrice($commodityCurrentPrice);
//            $order->addCommodity($c);
    }
//        $this->em->persist($order);
//        $this->em->flush();
//        (new SessionHelper($request->getSession()))->pushOrder($order);
//        return $order;
//    }
}
