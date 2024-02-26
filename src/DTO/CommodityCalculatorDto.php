<?php

declare(strict_types=1);


namespace App\DTO;


final readonly class CommodityCalculatorDto extends CalculatorComposite
{
    private \SplObjectStorage $storage;
    public function __construct()
    {
        $this->storage = new \SplObjectStorage();
    }

    public function sum(): int
    {
        $res = 0;
        foreach ($this->storage as $commodity){
            $res += $commodity->sum();
        }
        return $res;
    }

    public function add(CalculatorComposite $composite): void
    {
        $this->storage->attach($composite);
    }
}