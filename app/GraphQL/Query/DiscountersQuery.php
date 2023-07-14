<?php
namespace App\GraphQL\Query;

use GraphQL\Type\Definition\Type;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\GraphQL\RguysQuery;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\Discounter;

class DiscountersQuery extends RguysQuery
{
    protected $attributes = [
        'name' => 'discounters'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('Discounter'))));
    }

    public function resolve($root, $args)
    {
       return Discounter::all();
    }
}