<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\NumericFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\OpenApi\Model\Operation;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use phpDocumentor\Reflection\Types\Context;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            openapi: new Operation(
                         description: "Отримання довідника категорій. Категорії мають різний рівень(level), який визначає ієрархію - вкладеність. Найстарші категорії мають рівень 0, вкладені категорії у мають відповідно рівень 1,2 і т.д.",
                     ),
        ),
        new Post(
            openapi:  new Operation(description: "Операція вимагає авторизації на рівні адміністратора"),
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Get(),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
        new Put(security: "is_granted('ROLE_ADMIN')"),
        new Patch(security: "is_granted('ROLE_ADMIN')"),
    ],
    normalizationContext: ['groups' => ['category.read']],
    order: ['categoryOrder', 'id', 'children.categoryOrder']
)]
#[ApiFilter(BooleanFilter::class, properties: ['isActive'])]
#[ApiFilter(NumericFilter::class, properties: ['level'])]
#[UniqueEntity('categoryName')]
class Category
{

    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    #[Groups(['category.read'])]
    private ?Ulid $id;

    #[ORM\Column(nullable: true)]
    #[NotNull]
    #[Groups(['category.read'])]
    #[ApiProperty(
        description: 'рівень вкладеності категорії',
        openapiContext: [
            'type'    => 'integer',
            'example' => 1,
        ])]
    private ?int $level = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['category.read'])]
    #[NotNull]
    #[ApiProperty(description: 'порядок відображення категорії при видачі. Чим більше число categoryOrder тим менший приоритет у сортуванні', openapiContext: [
        'type'    => 'integer',
        'example' => 0,
    ])]
    private ?int $categoryOrder = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[ApiProperty(
        description: "поле необовʼязкове, у категорій найвищого рівня parent відсутній. Для категорій нижчого рівня поле рекомендується для використання при створенні ієрархії",
        openapiContext: [
            'type'    => 'string',
            'example' => '/api/categories/01HM19QGNNMCD3NXFAAQRYC1HV',
        ]
    )]
    private ?self $parent = null;

    #[ORM\Column(length: 255)]
    #[NotNull]
    #[Groups(['category.read'])]
    #[ApiProperty(description: "поле має бути унікальним", openapiContext: ['type' => 'string', 'max' => 255, 'example' => 'Вишиванки ручної роботи'])]
    private ?string $categoryName = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Product::class)]
    #[ApiProperty(
        description: "поле необовʼязкове, аналогічну операцію зручніше проводити при ручному вводі продуктів (POST /api/products)",
        openapiContext: [
            'type'    => 'array',
            'example' => ['/api/products/01HM19QGNNMCD3NXFAAQRYC1HD', '/api/products/01HM19QGNNMCD3NXFAAQRYC1HV'],
        ])]
    private Collection $products;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    #[Groups(['category.read'])]
    #[ApiProperty(
        description: "Може бути використано для задання усіх вкладених категорій, але це не рекомендовано. Аналогічну операцію зручніше проводити при створенні дочірніх категорій, використовуючи поле parent",
        openapiContext: [
            'type'    => 'array',
            'example' => ['/api/categories/01HM19QGNNMCD3NXFAAQRYC1HV'],
        ]
    )]
    private Collection $children;

    #[ORM\Column]
    #[Groups(['category.read', 'category.write'])]
    private bool $isActive = true;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getCategoryOrder(): ?int
    {
        return $this->categoryOrder;
    }

    public function setCategoryOrder(?int $categoryOrder): static
    {
        $this->categoryOrder = $categoryOrder;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    public function getCategoryName(): ?string
    {
        return $this->categoryName;
    }

    public function setCategoryName(string $categoryName): static
    {
        $this->categoryName = $categoryName;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (! $this->products->contains($product)) {
            $this->products->add($product);
            $product->setCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): static
    {
        if (! $this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): static
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function isIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }
}
