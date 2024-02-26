<?php

declare(strict_types=1);


namespace App\Convertors;


use Symfony\Component\Uid\Ulid;

class OrderHistoryStorageConvertor
{
    private array $history = [];

    public function __construct(array $historyNode)
    {
        $timeStamp = Ulid::generate();
        $this->history[$timeStamp] = $historyNode;
    }
    public function getHistory() : array
    {
        return $this->history;
    }

}