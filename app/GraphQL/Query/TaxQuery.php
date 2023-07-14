<?php
namespace App\GraphQL\Query;

use App\Tax;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\GraphQL\RguysQuery;
use Rebing\GraphQL\Support\Facades\GraphQL;

class TaxQuery extends RguysQuery
{
    protected $attributes = [
        'name' => 'Tax'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('Tax'))));
    }

    public function resolve($root, $args)
    {
       return Tax::all();
    }
}