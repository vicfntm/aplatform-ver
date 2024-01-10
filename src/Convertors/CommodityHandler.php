<?php

declare(strict_types=1);


namespace App\Convertors;


use App\Convertors\CommodityComposite;
use App\Enums\CommodityOperationType;

final class CommodityHandler extends CommodityComposite
{
    private object $children;
    private string $parentId = '';
    private int $price = 0;
    private int $amt = 0;

    public function __construct()
    {
        $this->children = new \SplObjectStorage();
    }

    public function add(CommodityComposite $component): void
    {
        $this->children->attach($component);
        $component->setParent($this);
    }

    public function operate(): array
    {
        if ($this->children->count() === 0) {
            return [];
        }
        foreach ($this->children as $child) {
            $this->processItems($child->operate());
        }
        if ($this->amt < 0) {
            return [];
        }

        return [
//            'id'     => $this->parentId,
            'amount' => $this->amt,
            'price'  => $this->price,
        ];
    }


    private function processItems(array $data): void
    {
        if ($data['type'] === CommodityOperationType::IMPORT->name) {
            $this->amt      += $data['amount'];
            $this->price    = $data['price'];
            $this->parentId = $data['id'];
        } else {
            $this->amt -= $data['amount'];
        }
    }
}
