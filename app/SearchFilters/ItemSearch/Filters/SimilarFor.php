<?php

namespace App\SearchFilters\ItemSearch\Filters;

use App\SearchFilter\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SimilarFor implements Filter
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
        return $builder->join('item_link as il', function ($join) use ($value) {
            $join->on('il.linked_item_id', '=', 'item.item_id');
            $join->on('il.cmf_site_id', '=', 'item.cmf_site_id');
            $join->on('il.item_link_type_id', DB::raw(4));
            $join->on('il.item_id', DB::raw($value));
        });
    }
}