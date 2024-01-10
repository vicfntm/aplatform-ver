<?php

declare(strict_types=1);


namespace App\Service;


use App\Contracts\Composite;

abstract class Counter implements Composite
{

    protected ?Composite $parent;

    public function setParent(?Composite $parent): void
    {
        $this->parent = $parent;
    }

    public function getParent(): ?Composite
    {
        return $this->parent;
    }

    public function add(Composite $composite):  void {}

    public function remove(Composite $composite): void {}

    abstract public function calculate();
}