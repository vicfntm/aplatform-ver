<?php

declare(strict_types=1);


namespace App\Exception;


use Throwable;

class NotEnoughStockException extends \Exception
{
    public const MESSAGE = 'not enough product stock for request';
    // Redefine the exception so message isn't optional
    public function __construct($message = '', $code = 406, Throwable $previous = null) {
        // some code

        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    // custom string representation of object
    public function __toString(): string
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}