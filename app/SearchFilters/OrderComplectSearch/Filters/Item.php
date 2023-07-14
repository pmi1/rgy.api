<?php
namespace App\SearchFilters\OrderComplect\Filters;

use App\SearchFilter\Filter;
use Illuminate\Database\Eloquent\Builder;

class Item implements Filter
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
        return $builder->where('orders_complect.item_id', '=', $value);
    }
}