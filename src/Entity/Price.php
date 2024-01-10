<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\PriceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Ulid;

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
    private ?int $price = null;

    #[ORM\ManyToOne(inversedBy: 'price')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['price.read', 'price.write'])]
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
