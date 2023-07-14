<?php
/**
 * Created by PhpStorm.
 * CustomerID: 
 * Date: 2019-05-31
 * Time: 15:38
 */

namespace App\SearchFilters\OrderLogistSearch;


use App\SearchFilter\Result;
use Illuminate\Database\Eloquent\Builder;

class OrderLogistSearchResult extends Result
{
    public function __construct(Builder $builder)
    {
        parent::__construct($builder);
        $this->addDefaultFields();
    }

    private function addDefaultFields()
    {
        $this->builder->select([
            'contractor_id',
            'address',
            'status',
        ]);
        return $this;
    }

    public function get()
    {
        return $this->builder->get();
    }

    public function getBuilder()
    {
        return $this->builder;
    }
}