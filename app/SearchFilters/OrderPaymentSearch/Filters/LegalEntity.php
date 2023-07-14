<?php
namespace App\SearchFilters\OrderPayment\Filters;

use App\SearchFilter\Filter;
use Illuminate\Database\Eloquent\Builder;

class LegalEntity implements Filter
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
        return $builder->where('orders_payment.legal_entity_id', '=', $value);
    }
}