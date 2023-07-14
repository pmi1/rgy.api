<?php

namespace App\SearchFilters\ItemSearch\Filters;

use App\SearchFilter\Filter;
use Illuminate\Database\Eloquent\Builder;

class Repair implements Filter
{
    /**
     * Apply a given search value to the builder instance.
     *
     * @param Builder $builder
     * @param mixed $value
     * @return Builder $builder
     */
    public static function apply(Builder $builder, $value)
    {
        // not repair
        if ($value == 1) {
            return $builder->where('repair','=', 0);
        }
        elseif ($value == 2) { // repair
            return $builder->where('repair', '>', 0);
        }
        return $builder;
    }
}