<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use App\Controller\OrderAction;
use App\Controller\UpdateOrderAction;
use App\Enums\CommodityOperationType;
use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\DocBlock\Description;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints\Choice;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Get(routePrefix: '/public'),
        new Post(
            routePrefix:            '/public',
            controller:             OrderAction::class,
            openapi:                new Operation(
                                        requestBody: new RequestBody(
                                                         description: new Description(
                                                                          "замовлення - масив обʼєктів з ключами кількості і відповідного продукту"
                                                                      ),
                                                         content:     new \ArrayObject(
                                                                          [
                                                                              'application/ld+json' => [
                                                                                  'schema' => [
                                                                                      'type'       => 'object',
                                                                                      'properties' => [
                                                                                          'delivery' => [
                                                                                              'type'       => 'object',
                                                                                              'properties' => [
                                                                                                  'transportType' => [
                                                                                                      'type'    => 'string',
                                                                                                      'enum'    => ['NOVA POSHTA'],
                                                                                                      'example' => 'NOVA POSHTA',
                                                                                                  ],
                                                                                                  'address'       => [
                                                                                                      'type'        => 'string',
                                                                                                      'example'     => '03127',
                                                                                                      'description' => 'номер поштового відділення',
                                                                                                  ],
                                                                                              ],
                                                                                          ],
                                                                                          'products' => [
                                                                                              'type'  => 'array',
                                                                                              'items' => [
                                                                                                  'properties' => [
                                                                                                      'product' => [
                                                                                                          'type'    => 'string',
                                                                                                          'example' => '01HM19QGN8349WS3B1666S4K6G',
                                                                                                      ],
                                                                                                      'amount'  => [
                                                                                                          'type'    => 'integer',
                                                                                                          'example' => 1,
                                                                                                      ],
                                                                                                  ],
                                                                                              ],
                                                                                          ],
                                                                                      ],
                                                                                  ],
                                                                              ],
                                                                          ]
                                                                      )
                                                     ),
                                    ),
            denormalizationContext: ['groups' => ['order.save']],
        ),
        new Patch(controller: UpdateOrderAction::class, denormalizationContext: ['groups' => ['order.update']]),
    ],
)]
class Order
{

    private const DEFAULT_STATUS = 'NEW';
    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    #[Groups(['order.read', 'public.order.read'])]
    private ?Ulid $id;

    #[ORM\OneToMany(mappedBy: 'relatedOrder', targetEntity: Commodity::class, cascade: ['persist'])]
    #[Groups(['public.order.read'])]
    private Collection $commodities;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['public.order.read', 'order.update'])]
    #[Choice([
        CommodityOperationType::CORRECTION->name,
        CommodityOperationType::SOLD->name,
        CommodityOperationType::RETURN->name,
        CommodityOperationType::PREORDER->name,
    ])]
    #[ApiProperty(
        description: "статус замовлення. При створенні покупцем присвоюється автоматично, при оновленні даних менеджером(адміністратором) може мати одне з константних значень", openapiContext: [
        'type'    => 'string',
        'example' => CommodityOperationType::CORRECTION->name,
    ]
    )]
    private ?string $status = null;

    #[Groups(['order.save', 'order.update'])]
    #[ApiProperty(
        openapiContext: [
            'type'  => 'array',
            'items' => [
                'properties' => [
                    'product' => ['type' => 'string', 'example' => '01HM19QGN8349WS3B1666S4K6G'],
                    'amount'  => ['type' => 'integer', 'example' => 1],

                ],
            ],
        ])]
    private array $products = [];

    #[ORM\OneToOne(mappedBy: 'customerOrder', cascade: ['persist', 'remove'])]
    #[Groups(['public.order.read'])]
    private ?Delivery $delivery = null;

    #[ORM\Column(nullable: true)]
    private ?array $history = null;

    public function __construct()
    {
        $this->products = [];
        $this->commodities = new ArrayCollection();
        $this->setStatus(CommodityOperationType::PREORDER->name);
    }

    public function getId(): ?Ulid
    {
        return $this->id;
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
            $commodity->setRelatedOrder($this);
        }

        return $this;
    }

    public function removeCommodity(Commodity $commodity): static
    {
        if ($this->commodities->removeElement($commodity)) {
            // set the owning side to null (unless already changed)
            if ($commodity->getRelatedOrder() === $this) {
                $commodity->setRelatedOrder(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return array
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @param array $products
     */
    public function setProducts(array $products): void
    {
        $this->products = $products;
    }

    public function getDelivery(): ?Delivery
    {
        return $this->delivery;
    }

    public function setDelivery(?Delivery $delivery): static
    {
        // unset the owning side of the relation if necessary
        if ($delivery === null && $this->delivery !== null) {
            $this->delivery->setCustomerOrder(null);
        }

        // set the owning side of the relation if necessary
        if ($delivery !== null && $delivery->getCustomerOrder() !== $this) {
            $delivery->setCustomerOrder($this);
        }

        $this->delivery = $delivery;

        return $this;
    }

    public function getHistory(): ?array
    {
        return $this->history;
    }

    public function setHistory(?array $history): static
    {
        $this->history = $history;

        return $this;
    }
}
