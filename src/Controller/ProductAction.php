<?php

declare(strict_types=1);


namespace App\Controller;


use App\Entity\Category;
use App\Entity\Product;
use App\Helper\UriParser;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Ulid;

class ProductAction extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    public function __invoke(Request $request)
    {
        $requestUri   = $request->query->get('category_id');
        $id           = UriParser::uidParser($requestUri);
        $categoryRepo = $this->manager->getRepository(Category::class);
        $res          = $categoryRepo->findOneBy(['id' => $id]);

        return $res->getProducts();
    }
}
