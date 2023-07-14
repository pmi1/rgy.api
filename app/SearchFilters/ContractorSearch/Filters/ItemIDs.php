<?php

namespace App\SearchFilters\ContractorSearch\Filters;

use App\SearchFilter\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

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
        return $builder->whereHas('itemContractor', function ($query) use ($value) {
            $query->where('item_contractor.cmf_site_id', config('common.siteId', 1))
                  ->whereIn('item_id', $value);
        });
    }
}