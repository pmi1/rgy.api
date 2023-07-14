<?php

namespace App\SearchFilters\ItemSearch\Filters;

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
        $value = trim($value);
        return $builder->where(function ($query) use ($value) {
            $query->where('article', '=', $value);
            $query->orWhere('name', 'like', '%' . $value . '%');
        });
    }
}