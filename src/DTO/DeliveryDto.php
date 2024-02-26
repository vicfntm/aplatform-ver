<?php

declare(strict_types=1);


namespace App\DTO;


final class DeliveryDto
{

    public function __construct(private string $transportType = '', private string $address = '')
    {
    }

    public function toArray(): array
    {
        return [
            'address'       => $this->address,
            'transportType' => $this->transportType,
        ];
    }

    public function getTransportType(): string
    {
        return $this->transportType;
    }

    public function setTransportType(string $transportType): void
    {
        $this->transportType = $transportType;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }


}