<?php

namespace App\Service;

use Symfony\Component\Uid\Ulid;

interface Finder
{
    public function find(Ulid $ulid): iterable;
}