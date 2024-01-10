<?php

declare(strict_types=1);


namespace App\Helper;


class UriParser
{
    public static function uidParser(string $uri): string
    {
        $res = explode('/', $uri);

        return array_pop($res);
    }
}
