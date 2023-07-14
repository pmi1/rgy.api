<?php
/**
 * Created by PhpStorm.
 * CustomerID: 
 * Date: 2019-05-31
 * Time: 15:38
 */

namespace App\SearchFilters\ContractorSearch;


use App\SearchFilter\Result;
use Illuminate\Database\Eloquent\Builder;

class ContractorSearchResult extends Result
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
            'name',
            'user_name as userName',
            'user_lastname as userLastName',
            'description',
            'image',
            'city',
            'address',
            'address_sklad as warehouseAddress',
            'site',
            'emails',
            'phone',
            'status',
            'force_delivery as forceDelivery',
            'max_discount_percent_for_contractor as maxDiscountPercentForContractor'

        ])
        ->where('cmf_site_id', config('common.siteId', 1));
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