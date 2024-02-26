<?php

declare(strict_types=1);


namespace App\DTO;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Uid\Ulid;

final class UpdateOrderDto
{

    private ArrayCollection $orderProducts;

    public function __construct(readonly array $products, private readonly string $orderId, readonly string $status = '')
    {
        $this->orderProducts = new ArrayCollection();
        foreach ($products as $product) {
            ['product' => $id, 'amount' => $amount] = $product;
            $this->orderProducts->add(new ProductDto(id: $id, amt: $amount));
        }
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\App\DTO\ProductDto>
     */
    public function getOrderProducts(): ArrayCollection
    {
        return $this->orderProducts;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getOrderIdConvertedToRfc() : string
    {
        return Ulid::fromString($this->getOrderId())->toRfc4122();
    }





}