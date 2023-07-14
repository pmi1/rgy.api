<?php
namespace App\GraphQL\Query;

use App\OrdersCancelReason;
use GraphQL\Type\Definition\Type;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\GraphQL\RguysQuery;
use Rebing\GraphQL\Support\Facades\GraphQL;

class OrderCancelReasonsQuery extends RguysQuery
{
    protected $attributes = [
        'name' => 'orderCancelReasons'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('OrderCancelReason'))));
    }

    public function resolve($root, $args)
    {
        return OrdersCancelReason::all();
    }
}