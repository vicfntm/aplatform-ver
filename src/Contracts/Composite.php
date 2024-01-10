<?php

namespace App\Contracts;

interface Composite
{
    public function setParent(?Composite $parent) : void;
    public function getParent() : ?Composite;
    public function add(Composite $composite) : void;
    public function remove(Composite $composite) : void;
    public function calculate();
}