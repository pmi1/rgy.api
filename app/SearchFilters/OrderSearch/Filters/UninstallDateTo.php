<?php

namespace App\SearchFilters\OrderSearch\Filters;

use App\SearchFilter\Filter;
use Illuminate\Database\Eloquent\Builder;

class UninstallDateTo implements Filter
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
        return $builder->whereRaw('STR_TO_DATE(enddate, \'%Y-%m-%d\') <=?', [$value]);;
    }
}