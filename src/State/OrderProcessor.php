<?php

declare(strict_types=1);


namespace App\State;


use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;

class OrderProcessor implements ProcessorInterface
{

    /**
     * @param \App\DTO\OrderDto $data
     * @param \ApiPlatform\Metadata\Operation $operation
     * @param array $uriVariables
     * @param array $context
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $d = null;
        // TODO: Implement process() method.
    }
}