<?php
namespace App\GraphQL\Query;

use App\OrderEventType;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\GraphQL\RguysQuery;
use Rebing\GraphQL\Support\Facades\GraphQL;

class OrderEventtypeQuery extends RguysQuery
{
    protected $attributes = [
        'name' => 'orderEventType'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('OrderEventType'))));
    }

    public function resolve($root, $args)
    {
        return OrderEventType::all();
    }
}