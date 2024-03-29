<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\ProductAction;
use App\Convertors\CommodityHandler;
use App\Convertors\CommodityStorage;
use App\DTO\AggregationDto;
use App\Enums\CommodityOperationType;
use App\Repository\ProductRepository;
use App\Service\CommodityCounter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints\NotNull;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(controller: ProductAction::class),
        new Get(),
        new Post(security: "is_granted('ROLE_ADMIN') "),
        new Patch(security: "is_granted('ROLE_ADMIN') "),
    ],
    normalizationContext: ['groups' => ['product.read']],
    denormalizationContext: ['groups' => ['product.write']],
)]
#[ApiFilter(BooleanFilter::class, properties: ['isActive'])]
#[ApiFilter(SearchFilter::class, properties: ['category.id' => 'exact'])]
class Product
{

    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    #[Groups(['product.read', 'product.write', 'media.read', 'media.write', 'category.read', 'public.order.read'])]
    private ?Ulid $id;

    #[ORM\Column]
    #[Groups(['product.read', 'product.write', 'commodity.read', 'category.read'])]
    #[ApiProperty(
        description: "Видалення продуктів є концептуально небажаним, хоча і можливим. Натомість рекомендована оерація - деактивація продукту шляхом зміни булевого статусу",
        openapiContext: [
            'type'    => 'boolean',
            'example' => true,
        ]
    )]
    private ?bool $isActive = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product.read', 'product.write', 'commodity.read', 'category.read', 'public.order.read'])]
    #[NotNull]
    #[ApiProperty(
        description: "не більше 255 символів",
        openapiContext: [
            'type' => 'string',
            'maxLength' => 255,
        ]
    )]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['product.read', 'product.write', 'category.read'])]
    #[ApiProperty(
        description: "повнотекстова інформація про товар",
        openapiContext: [
            'type' => 'string',
            'maxLength'  => 65000,
        ]
    )]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Commodity::class, orphanRemoval: true)]
//    #[Groups(['product.read', 'category.read'])]
    #[ORM\OrderBy(['id' => 'ASC'])]
    private Collection $commodities;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Media::class)]
    #[Groups(['product.read', 'product', 'commodity.read', 'category.read', 'public.order.read'])]
    private Collection $media;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[Groups(['product.write', 'product.read', 'public.order.read'])]
    #[ApiProperty(
        description: "посилання на категорію, до якої відноситься продукт",
        openapiContext: [
            'type'    => 'string',
            'example' => '/api/categories/01HM19QGN8349WS3B1666S4K6F',
        ]
    )]
    private ?Category $category = null;


    public function __construct()
    {
        $this->commodities = new ArrayCollection();
        $this->media = new ArrayCollection();
    }

    #[Groups(['product.read', 'category.read'])]
    public function getCommodityAggregated(): iterable
    {
        /**
         * @var Commodity[] $commodityCollection
         */
        $commodityCollection = $this->commodities;
        $handler = new CommodityHandler();
        $lastCommodityActivitySet = (new CommodityCounter())->withLastCommoditySet($commodityCollection);
        foreach ($lastCommodityActivitySet as $c) {
            ['cmd' => $commodity] = $c;
            $price = $commodity->getPrice()->last();
            $storage = new CommodityStorage(
                type:   $commodity->getOperationType(),
                amount: $this->ag->getAmount(),
                id:     $commodity->getId(),
                price:  $price ? $price->getPrice() : $price
            );
            $handler->add($storage);
        }

        return $handler->operate();
    }

    private AggregationDto $ag;
    public function withAggregationParams(AggregationDto $ag) : void
    {
        $this->ag = $ag;
    }
    public function getAParams () : AggregationDto
    {
        return $this->ag;
    }

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Commodity>
     */
    public function getCommodities(): Collection
    {
        return $this->commodities;
    }

    public function addCommodity(Commodity $commodity): static
    {
        if (! $this->commodities->contains($commodity)) {
            $this->commodities->add($commodity);
            $commodity->setProduct($this);
        }

        return $this;
    }

    public function removeCommodity(Commodity $commodity): static
    {
        if ($this->commodities->removeElement($commodity)) {
            // set the owning side to null (unless already changed)
            if ($commodity->getProduct() === $this) {
                $commodity->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): static
    {
        if (! $this->media->contains($medium)) {
            $this->media->add($medium);
            $medium->setProduct($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): static
    {
        if ($this->media->removeElement($medium)) {
            // set the owning side to null (unless already changed)
            if ($medium->getProduct() === $this) {
                $medium->setProduct(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }


}
