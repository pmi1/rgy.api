<?php

namespace App\SearchFilters\OrderSearch\Filters;

use App\SearchFilter\Filter;
use Illuminate\Database\Eloquent\Builder;

class Query implements Filter
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
        return $builder->where(function($query) use ($value) {
            $query->whereHas('customer', function ($query) use($value){
                $query->where('name', 'like', "%{$value}%");
                $query->orWhere('secondname', 'like', "%{$value}%");
                $query->orWhere('lastname', 'like', "%{$value}%");
                $query->orWhere('email', 'like', "%{$value}%");
                $query->orWhere('phone', 'like', "%{$value}%");
            })
            ->orWhere('orders.orders_id', '=', $value);
        });
    }
}