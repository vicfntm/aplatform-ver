<?php

declare(strict_types=1);


namespace App\DTO;


readonly abstract class CalculatorComposite
{

    abstract public function sum() : int;


}