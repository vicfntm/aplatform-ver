<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use App\Controller\CommodityAction;
use App\Enums\CommodityOperationType;
use App\Repository\CommodityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\DocBlock\Description;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints\NotNull;

#[
    ORM\Entity(repositoryClass: CommodityRepository::class)]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_ADMIN')"),
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
        //        new GetCollection(
        //            routePrefix: '/public',
        //        ),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
        new Post(security: "is_granted('ROLE_ADMIN')"),
        new Post(
            routePrefix: '/public', controller: CommodityAction::class, openapi: new Operation(
            description: "Метод використовується для збору інформації про товари, з якими взаємодіяв споживач. Передається product id. Запит являє собою масив обʼєктів. Кількість елементів масиву визначається фронтендом.",
            requestBody: new RequestBody(

                             content: new \ArrayObject(
                                          [
                                              'application/ld+json' => [

                                                  'schema' => [
                                                      'type'  => 'array',
                                                      'items' => [
                                                          'properties' => [
                                                              'product' => [
                                                                  'type'    => 'string',
                                                                  'example' => '01HM19QGNNMCD3NXFAAQRYC1HS',
                                                              ],
                                                          ],
                                                      ],
                                                  ],
                                              ],
                                          ]

                                      )
                         )
        )
        ),
    ],
    normalizationContext: ['groups' => ['commodity.read']],
    denormalizationContext: ['groups' => ['commodity.write']],
)]
class Commodity
{

    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    #[Groups(['read', 'product.read', 'public.order.read'])]
    private ?Ulid $id;

    #[ORM\Column(length: 255)]
    #[Groups(['commodity.write', 'product.read', 'commodity.read', 'public.order.read'])]
    #[NotNull]
    #[ApiProperty(
        description: "commodity є відображенням дії по руху ТМЦ в системі і обовʼязково мають певний тип", openapiContext: [
        'type' => 'string',
        'enum' => [
            CommodityOperationType::IMPORT->name,
            CommodityOperationType::PREORDER->name,
            CommodityOperationType::SOLD->name,
            CommodityOperationType::RETURN->name,
        ],
        'example' => CommodityOperationType::IMPORT->name
    ]
    )]
    private ?string $operationType = null;

    #[ORM\Column]
    #[Groups(['commodity.write', 'product.read', 'commodity.read', 'public.order.read'])]
    #[NotNull]
    private ?int $amount = null;

    #[ORM\ManyToOne(inversedBy: 'commodities')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['commodity.write', 'commodity.read', 'public.order.read'])]
    #[NotNull]
    private ?Product $product = null;

    #[ORM\OneToMany(mappedBy: 'commodity', targetEntity: Price::class, cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['product.read', 'commodity.read'])]
    private Collection $price;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[Groups(['commodity.update'])]
    private ?self $CommoditySource = null;

    #[ORM\ManyToOne(inversedBy: 'commodities')]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'commodities')]
    private ?Order $relatedOrder = null;

    public function __construct()
    {
        $this->price = new ArrayCollection();
    }

    #[Groups(['public.order.read'])]
    #[SerializedName('price')]
    public function getChildPriceFromParentCommodity(): ?int
    {
        return $this->getCommoditySource()->getSinglePrice();
    }

    public function getSinglePrice(): ?int
    {
        /**
         * @var Price|bool $priceCollection
         */
        $priceCollection = $this->price->last();

        return $priceCollection ? $priceCollection->getPrice() : $priceCollection;
    }

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getOperationType(): ?string
    {
        return $this->operationType;
    }

    public function setOperationType(string $operationType): static
    {
        $this->operationType = $operationType;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Collection<int, Price>
     */
    public function getPrice(): Collection
    {
        return $this->price;
    }

    public function addPrice(Price $price): static
    {
        if (! $this->price->contains($price)) {
            $this->price->add($price);
            $price->setCommodity($this);
        }

        return $this;
    }

    public function removePrice(Price $price): static
    {
        if ($this->price->removeElement($price)) {
            // set the owning side to null (unless already changed)
            if ($price->getCommodity() === $this) {
                $price->setCommodity(null);
            }
        }

        return $this;
    }

    public function getCommoditySource(): ?self
    {
        return $this->CommoditySource;
    }

    public function setCommoditySource(?self $CommoditySource): static
    {
        $this->CommoditySource = $CommoditySource;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getRelatedOrder(): ?Order
    {
        return $this->relatedOrder;
    }

    public function setRelatedOrder(?Order $relatedOrder): static
    {
        $this->relatedOrder = $relatedOrder;

        return $this;
    }
}
