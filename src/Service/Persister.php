<?php

declare(strict_types=1);


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;

readonly class Persister
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function persistEntity(object $entity) : void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}