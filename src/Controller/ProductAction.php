<?php

declare(strict_types=1);


namespace App\Controller;


use App\Service\Finder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Ulid;

class ProductAction extends AbstractController
{

    public function __construct(private readonly Finder $finder)
    {
    }

    public function __invoke(Request $request)
    {
        $requestCatId = new Ulid($request->query->get('category_id'));
        return $this->finder->find($requestCatId);
    }
}
