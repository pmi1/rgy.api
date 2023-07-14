<?php
namespace App\SearchFilters\OrderComplectSearch;

use App\OrderComplect;
use App\SearchFilter\Result;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\SearchFilters\SearchAbstract;

class OrderComplectSearch extends SearchAbstract
{
    /**
     * @param Builder $query
     * @return UserSearchResult
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
        return __NAMESPACE__ . '\\OrderComplectSearchResult';
    }

    /**
     * @param array $filters
     * @return Builder
     */
    public static function apply(array $filters)
    {
        return static::applyDecoratorsFromRequest($filters, (new OrderComplect())->newQuery());
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