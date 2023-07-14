<?php

namespace App\SearchFilters\ItemSearch\Filters;

use App\SearchFilter\Filter;
use Illuminate\Database\Eloquent\Builder;

class ContractorID implements Filter
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

        return $builder->leftJoin('item_contractor as ic', function ($join) {
                    $join->on('ic.item_id', '=', 'item.item_id');
                    $join->on('ic.cmf_site_id', '=', 'item.cmf_site_id');
                })->whereIn('ic.contractor_id', $value);
    }
}