<?php
namespace App\GraphQL\Query;

use App\OrderRentRefundReason;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\GraphQL\RguysQuery;
use Rebing\GraphQL\Support\Facades\GraphQL;

class OrderRentRefundReasonQuery extends RguysQuery
{
    protected $attributes = [
        'name' => 'orderRentRefundReason'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('OrderRentRefundReason'))));
    }

    public function resolve($root, $args)
    {
        return OrderRentRefundReason::all();
    }
}