<?php

namespace App\SearchFilters\OrderLogistSearch\Filters;

use App\SearchFilter\Filter;
use Illuminate\Database\Eloquent\Builder;
use App\Order;

class OrderStatus implements Filter
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
        if (!is_array($value)) {
            $value = [$value];
        }
        return $builder->whereIn('orders_logist.orders_id', function($query) use($value){
                    $query->select('orders_id')
                    ->from(with(new Order)->getTable())
                    ->whereIn('orders_status_id', $value);
                });
    }
}