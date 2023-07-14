<?php

namespace App\SearchFilters\ItemSearch\Filters;

use App\SearchFilter\Filter;
use Illuminate\Database\Eloquent\Builder;

class CatalogueID implements Filter
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
        return $builder->leftJoin('cat_item_link as c', function ($join) {
                    $join->on('c.item_id', '=', 'item.item_id');
                    $join->on('c.cmf_site_id', '=', 'item.cmf_site_id');
                })->where('c.catalogue_id', '=', $value);
    }
}