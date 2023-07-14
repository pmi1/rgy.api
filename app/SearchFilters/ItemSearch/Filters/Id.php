<?php
namespace App\SearchFilters\ItemSearch\Filters;

use App\SearchFilter\Filter;
use Illuminate\Database\Eloquent\Builder;

class Id implements Filter
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

        return $builder->whereIn('item.item_id', $value);
    }
}