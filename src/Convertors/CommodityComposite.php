<?php

declare(strict_types=1);


namespace App\Convertors;


abstract class CommodityComposite
{
    /**
     * @var CommodityComposite|null
     */
    protected  ?CommodityComposite $parent;

    public function setParent(?CommodityComposite $parent): void
    {
        $this->parent = $parent;
    }

    public function getParent(): CommodityComposite
    {
        return $this->parent;
    }

    public function add(CommodityComposite $component): void
    {
    }

    abstract public function operate(): array;
}
