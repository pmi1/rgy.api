<?php

namespace App\SearchFilters\OrderComplectSearch;


use App\OrderComplect;
use App\SearchFilter\Result;
use function foo\func;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class OrderComplectSearchResult extends Result
{
    public function __construct(Builder $builder)
    {
        parent::__construct($builder);
        $this->addDefaultFields();
    }

    public function distinct()
    {
        $this->builder->distinct();
    }

    public function paginate($countItemsPerPage = null)
    {
        $countItemsPerPage = $countItemsPerPage?$countItemsPerPage:env('PAGINATION_COUNT_PER_PAGE');
        $paginate = $this->builder->paginate($countItemsPerPage);
        $this->mapResult($paginate->getCollection());
        return $paginate;
    }

    public function get()
    {
        $limitItems = env('LIMIT_ITEMS', 1000);
        return $this->builder->limit($limitItems)->get();
    }

    public function addTotalAmount()
    {

    }

    public function order($orderType = 'id', $orderDirection = 'desc')
    {
        $this->builder->orderBy($orderType, $orderDirection);
        return $this;
    }

    private function mapResult($collection)
    {
    }
}