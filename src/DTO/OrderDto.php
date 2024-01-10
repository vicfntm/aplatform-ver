<?php

declare(strict_types=1);


namespace App\DTO;


use Doctrine\Common\Collections\ArrayCollection;

final class OrderDto
{

    private ?string $transportType;
    private ?string $address;
    private ArrayCollection $orderProducts;

    public function __construct(
        /** @noinspection PhpPropertyOnlyWrittenInspection */ private readonly array $products,
        private readonly array $delivery
    ) {
        $this->orderProducts = new ArrayCollection();

        foreach ($products as $product) {
            ['product' => $id, 'amount' => $amount] = $product;
            $this->orderProducts->add(new ProductDto(id: $id, amt: $amount));
        }
        ['transportType' => $this->transportType, 'address' => $this->address] = $delivery;
    }

    public function getOrderProducts(): ArrayCollection
    {
        return $this->orderProducts;
    }

    public function getTransportType(): ?string
    {
        return $this->transportType;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

}