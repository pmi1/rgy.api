<?php
namespace App\SearchFilters\UserSearch\Filters;

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
        return $builder->whereRaw("
                (user.user_id = '$value' or user.phone like '%$value%' or user.email like '%$value%'
                     or user.name like '%$value%' or user.secondname like '%$value%' or user.lastname like '%$value%' 
                     or concat(user.lastname, ' ', user.name, ' ', user.secondname) like '%$value%')
            ");
    }
}