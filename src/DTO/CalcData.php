<?php

declare(strict_types=1);


namespace App\DTO;

readonly final class CalcData extends CalculatorComposite
{
    public function __construct(private int $amount)
    {
    }

    public function sum(): int
    {
        return $this->amount;
    }
}