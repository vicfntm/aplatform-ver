<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\PriceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints\NotNull;

#[ORM\Entity(repositoryClass: PriceRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(security: "is_granted('ROLE_ADMIN') "),
        new Get(security: "is_granted('ROLE_ADMIN') "),
        new Post(security: "is_granted('ROLE_ADMIN') "),
    ],
    normalizationContext: ['groups' => ['price.read']],
    denormalizationContext: ['groups' => ['price.write']],
)]
class Price
{
    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    #[Groups(['price.read', 'product.read', 'commodity.read'])]
    private ?Ulid $id;

    #[ORM\Column]
    #[Groups(['read', 'price.write', 'product.read', 'commodity.read'])]
    #[ApiProperty(
        description: "первинна форма передачі - зберігання є цілі числа (ціна у копійках)",
        openapiContext: [
            'type' => 'integer',
            'example' => 10000
        ]
    )]
    private ?int $price = null;

    #[ORM\ManyToOne(inversedBy: 'price')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['price.read', 'price.write'])]
    #[NotNull]
    #[ApiProperty(
        description: "посилання на відповідну сутність commodity",
        openapiContext: [
            'type' => 'string',
            'example' => '/api/commodities/01HM19QGNBCTR5MNX3W3K3N7WG'
        ]
    )]
    private ?Commodity $commodity = null;

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCommodity(): ?Commodity
    {
        return $this->commodity;
    }

    public function setCommodity(?Commodity $commodity): static
    {
        $this->commodity = $commodity;

        return $this;
    }
}
