<?php
/**
 * Created by PhpStorm.
 * CustomerID: 
 * Date: 2019-05-31
 * Time: 16:04
 */

namespace App\SearchFilters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class SearchAbstract
{
    abstract static public function apply(array $request);
    abstract static protected function getResults(Builder $query);
}