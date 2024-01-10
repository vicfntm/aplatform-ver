<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Entity\Media;
use App\Helper\UriParser;

#[AsController]
final class CreateMediaObjectAction extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    public function __invoke(Request $request): Media
    {
        $o            = $this->manager->getRepository(Product::class);
        $q            = $o->find(UriParser::uidParser($request->get('product')));
        $uploadedFile = $request->files->get('file');
        $type         = $request->get('binaryType');
        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }
        $mediaObject = new Media();
        $mediaObject->setProduct($q);
        $mediaObject->file = $uploadedFile;
        $mediaObject->setBinaryType($type);
        $mediaObject->setIsMain((boolean)$request->get('isMain'));
        return $mediaObject;
    }


}
