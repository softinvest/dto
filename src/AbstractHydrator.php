<?php

namespace SoftInvest\DTO;

use Closure;

class AbstractHydrator
{
    public static function getHydrator(Closure $callback, $className)
    {
        $arr = $callback();

        $result = $arr ? $className::hydrate([$arr->id => (array)$arr]) : null;

        return $result !== null ? $result?->first() : null;
    }
}