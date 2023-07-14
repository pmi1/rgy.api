<?php

namespace App\SearchFilters\ItemSearch\Filters;

use App\SearchFilter\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AmountTo implements Filter
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
        return $builder->whereRaw('CONVERT(item.availablecount, UNSIGNED INTEGER) <= ?', [$value]);
    }
}