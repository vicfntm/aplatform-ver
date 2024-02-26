<?php

declare(strict_types=1);


namespace App\Convertors;


use App\Convertors\CommodityComposite;
use App\Enums\CommodityOperationType;
use App\Service\CommodityCounter;

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
        $res = [];
        /**
         * @var CommodityComposite $child
         */
        foreach ($this->children as $child) {
            $res[] = $child->operate();
//            $this->processItems($child->operate());
        }
        $this->processItems($res);
        if ($this->amt < 0) {
            return [];
        }

        return [
//            'id'     => $this->parentId,
'amount' => $this->amt,
'price'  => $this->price,
        ];
    }


    private function processItems(array $dt): void
    {
        foreach ($dt as $data) {
            if ($data['type'] === CommodityOperationType::IMPORT->name) {
                $this->amt += $data['amount'];
                $this->price = $data['price'] ?: 0;
                $this->parentId = $data['id'];
            } else {
                $this->amt -= $data['amount'];
            }
        }
    }
}
