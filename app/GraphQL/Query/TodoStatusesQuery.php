<?php
namespace App\GraphQL\Query;

use App\TodoStatus;
use GraphQL\Type\Definition\Type;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\GraphQL\RguysQuery;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\Discounter;

class TodoStatusesQuery extends RguysQuery
{
    protected $attributes = [
        'name' => 'todoStatuses'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('TodoStatus'))));
    }

    public function resolve($root, $args)
    {
       return TodoStatus::all();
    }
}