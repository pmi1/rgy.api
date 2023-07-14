<?php
namespace App\GraphQL\Query;

use App\TodoState;
use GraphQL\Type\Definition\Type;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\GraphQL\RguysQuery;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\Discounter;

class TodoStatesQuery extends RguysQuery
{
    protected $attributes = [
        'name' => 'todoStates'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('TodoState'))));
    }

    public function resolve($root, $args)
    {
       return TodoState::all();
    }
}