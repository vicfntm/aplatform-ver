<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\DeliveryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints\Choice;

#[ORM\Entity(repositoryClass: DeliveryRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
    ]
)]
class Delivery
{
    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    private ?Ulid $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[ApiProperty(
        description: "має бути вказано номер відділення Нової Пошти чи іншого поштового оператора",
        openapiContext: [
            'type' => 'string',
            'maxLength' => 255,
            'example' => '32261'
        ]
    )]
    private ?string $address = null;

    #[ORM\Column(length: 30, nullable: true)]
    #[Choice(['NOVA POSHTA'])]
    private ?string $transportCompany = null;

    #[ORM\OneToOne(inversedBy: 'delivery', cascade: ['persist', 'remove'])]
    private ?Order $customerOrder = null;

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getTransportCompany(): ?string
    {
        return $this->transportCompany;
    }

    public function setTransportCompany(?string $transportCompany): static
    {
        $this->transportCompany = $transportCompany;

        return $this;
    }

    public function getCustomerOrder(): ?Order
    {
        return $this->customerOrder;
    }

    public function setCustomerOrder(?Order $customerOrder): static
    {
        $this->customerOrder = $customerOrder;

        return $this;
    }
}
