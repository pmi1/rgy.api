<?php
/**
 * Created by PhpStorm.
 * CustomerID: 
 * Date: 2019-05-30
 * Time: 11:32
 */

namespace App\SearchFilters\OrderSearch;

use App\Item;
use App\Order;
use App\UserSearch\Filters\Result;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\SearchFilters\SearchAbstract;

class OrderSearch extends SearchAbstract
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
        return __NAMESPACE__ . '\\OrderSearchResult';
    }

    /**
     * @param array $filters
     * @return Builder
     */
    public static function apply(array $filters)
    {
        $query = static::applyDecoratorsFromRequest($filters, (new Order())->newQuery());
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