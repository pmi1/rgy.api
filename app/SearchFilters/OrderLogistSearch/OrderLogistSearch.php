<?php

namespace App\SearchFilters\OrderLogistSearch;

use App\OrderLogist;
use App\Order;
use App\UserSearch\Filters\Result;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\SearchFilters\SearchAbstract;

class OrderLogistSearch extends SearchAbstract
{
    /**
     * @param Builder $query
     * @return OrderSearchResult
     */
    protected static function getResults(Builder $query)
    {
        $classNameResult = self::getClassNameResult();
        $classObjectResult = new $classNameResult($query);
        return $classObjectResult;
    }

    /**
     * @return string
     */
    private static function getClassNameResult()
    {
        return __NAMESPACE__ . '\\OrderLogistSearchResult';
    }

    /**
     * @param array $filters
     * @return Builder
     */
    public static function apply(array $filters)
    {
        $query = static::applyDecoratorsFromRequest($filters, (new OrderLogist())->newQuery());
        return $query;
    }

    /**
     * @param array $filters
     * @param Builder $query
     * @return Builder
     */
    private static function applyDecoratorsFromRequest(array $filters, Builder $query)
    {
        foreach ($filters as $filterName => $value) {
            $decorator = static::createFilterDecorator($filterName);
            if (static::isValidDecorator($decorator)) {
                $query = $decorator::apply($query, $value);
            }
        }
        return $query;
    }

    private static function createFilterDecorator($name)
    {
        return __NAMESPACE__ . '\\Filters\\' . studly_case($name);
    }

    private static function isValidDecorator($decorator)
    {
        return class_exists($decorator);
    }
}