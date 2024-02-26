<?php

declare(strict_types=1);


namespace App\DTO;


use Symfony\Component\Uid\Ulid;

final readonly class ProductDto
{
    public function __construct(private string $id, private int $amt)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAmount(): int
    {
        return $this->amt;
    }

    public function getIdAsRfc() : string
    {
        return (new Ulid($this->id))->toRfc4122();
    }
}