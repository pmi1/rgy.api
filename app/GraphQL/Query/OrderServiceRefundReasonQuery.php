<?php
namespace App\GraphQL\Query;

use App\OrderServiceRefundReason;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\GraphQL\RguysQuery;
use Rebing\GraphQL\Support\Facades\GraphQL;

class OrderServiceRefundReasonQuery extends RguysQuery
{
    protected $attributes = [
        'name' => 'orderServiceRefundReason'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('OrderServiceRefundReason'))));
    }

    public function resolve($root, $args)
    {
        return OrderServiceRefundReason::all();
    }
}