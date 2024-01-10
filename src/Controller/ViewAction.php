<?php

declare(strict_types=1);


namespace App\Controller;


use App\Entity\Product;
use App\Entity\Views;
use App\Helper\UriParser;
use App\Repository\CommodityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ViewAction extends AbstractController
{
    private const STATUS = 'SALE';
    private CommodityRepository|\Doctrine\ORM\EntityRepository $viewRepo;
    private CommodityRepository|\Doctrine\ORM\EntityRepository $productRepo;

    public function __construct(private readonly EntityManagerInterface $em)
    {
        $this->viewRepo    = $this->em->getRepository(Views::class);
        $this->productRepo = $this->em->getRepository(Product::class);
    }

    public function __invoke(Request $request): object
    {
        $data = $request->toArray();
        if (empty($sessionId = $request->getSession()->getId())) {
            $request->getSession()->start();
            $sessionId = $request->getSession()->getId();
        }
        $product   = $this->productRepo->findOneBy(['id' => UriParser::uidParser($data['product'])]);
        $view      = new Views();
        $view->setSessionId($sessionId);
        $view->setProduct($product);
        $this->em->persist($view);
        $this->em->flush();

        return new class() {
        };
    }
}
