<?php

namespace App\SearchFilters\OrderLogistSearch\Filters;

use App\SearchFilter\Filter;
use Illuminate\Database\Eloquent\Builder;

class To implements Filter
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
        return $builder->where('orders_logist.cdate', '<=', date('Y-m-d 23:59', strtotime($value)));
    }
}