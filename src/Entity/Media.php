<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Enums\MediaTypes;
use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Ulid;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Controller\CreateMediaObjectAction;
use ApiPlatform\OpenApi\Model;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[ApiResource(
    types: ['https://schema.org/MediaObject'],
    operations: [
        new Get(security: "is_granted('ROLE_ADMIN') "),
        new GetCollection(security: "is_granted('ROLE_ADMIN') "),
        new Delete(security: "is_granted('ROLE_ADMIN') "),
        new Post(
            controller:        CreateMediaObjectAction::class,
            openapi:           new Model\Operation(
                                   requestBody: new Model\RequestBody(
                                                    content: new \ArrayObject(
                                                                 [
                                                                     'multipart/form-data' => [
                                                                         'schema' => [
                                                                             'type'       => 'object',
                                                                             'properties' => [
                                                                                 'file'       => [
                                                                                     'type'   => 'string',
                                                                                     'format' => 'binary',
                                                                                 ],
                                                                                 'binaryType' => [
                                                                                     'type' => 'string',
                                                                                 ],
                                                                                 'isMain'     => [
                                                                                     'type' => 'boolean',
                                                                                 ],
                                                                                 'product'    => [
                                                                                     'type' => 'string',
                                                                                 ],
                                                                             ],
                                                                         ],
                                                                     ],
                                                                 ]
                                                             )
                                                )
                               ),
            security:          "is_granted('ROLE_ADMIN') ",
            validationContext: ['groups' => ['Default', 'media_object_create']],
            deserialize:       false
        ),
    ],
    normalizationContext: ['groups' => ['media.read']],
    denormalizationContext: ['groups' => ['media.write']],

)]
class Media
{

    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    #[Groups(['media.read', 'product.read', 'public.order.read'])]
    private ?Ulid $id;

    #[ORM\Column(length: 255)]
    #[Groups(['media.write', 'media.read', 'product.read', 'commodity.read', 'public.order.read'])]
    #[Assert\NotNull]
    #[ApiProperty(
        openapiContext: [
            'type'    => 'string',
            'enum'    => [MediaTypes::JPEG->name, MediaTypes::MP4->name, MediaTypes::MPEG->name],
            'example' => MediaTypes::JPEG->name,
        ]
    )]
    private ?string $binaryType = null;

    #[ORM\Column(length: 255)]
    #[Groups(['media.write', 'media.read', 'product.read', 'commodity.read', 'public.order.read'])]
    #[ApiProperty(
        description: "відносне посилання на публічний доступ до медіаресурсу",
        openapiContext: [
            'type' => 'string',
            'example' => '/images/products/82-6506f99c2f691802579777.jpg'
        ]
    )]
    private ?string $binarySource = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[ApiProperty(types: ['https://schema.org/product'])]
    #[Groups(['media.write', 'media.read'])]
    private ?Product $product = null;

    #[ORM\Column]
    #[ApiProperty(
        description: "індикатор, що вказує, чи є медіаконтент таким, що демонструється як головне зображення-відео на сторінці"
    )]
    #[Groups(['media.write', 'media.read', 'commodity.read', 'product.read', 'public.order.read'])]
    private ?bool $isMain = null;

    #[ApiProperty(types: ['https://schema.org/contentUrl'])]
//    #[Groups(['media.read', 'media.read', 'public.order.read'])]
    public ?string $contentUrl = null;

    #[Vich\UploadableField(mapping: "products", fileNameProperty: "binarySource")]
    #[Assert\NotNull(groups: ['media_object_create'])]
    public ?File $file = null;


    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getBinaryType(): ?string
    {
        return $this->binaryType;
    }

    public function setBinaryType(string $binaryType): static
    {
        $this->binaryType = $binaryType;

        return $this;
    }

    public function getBinarySource(): ?string
    {
        return $this->binarySource;
    }

    public function setBinarySource(?string $binarySource): static
    {
        $this->binarySource = $binarySource;

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

    public function isIsMain(): ?bool
    {
        return $this->isMain;
    }

    public function setIsMain(bool $isMain): static
    {
        $this->isMain = $isMain;

        return $this;
    }
}