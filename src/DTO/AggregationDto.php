<?php

declare(strict_types=1);


namespace App\DTO;


readonly final class AggregationDto
{
    public function __construct(private int $amount)
    {
    }

    public function getAmount(): int
    {
        return $this->amount;
    }


}