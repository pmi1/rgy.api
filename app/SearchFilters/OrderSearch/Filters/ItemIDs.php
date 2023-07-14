<?php

namespace App\SearchFilters\OrderSearch\Filters;

use App\SearchFilter\Filter;
use Illuminate\Database\Eloquent\Builder;
use App\OrdersItem;

class ItemIDs implements Filter
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
        if (is_string($value)) {
            $value = json_decode($value);
        }

        if (is_int($value)) {
            $value = [$value];
        }
        $orderIDs = (new OrdersItem)->newQuery()->whereIn('item_id', $value)->get(['orders_id'])->toArray();
        return $builder->whereHas('ordersItem', function ($query) use($orderIDs){
           $query->whereIn('orders_id', $orderIDs);
        });
    }
}