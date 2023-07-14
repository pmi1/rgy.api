<?php
namespace App\GraphQL\Query;

use Closure;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\GraphQL\Query\OrdersQuery;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\SelectFields;
use Rebing\GraphQL\Support\Type as GraphQLType;


class OrderTotalChargeQuery extends OrdersQuery
{
    protected $attributes = [
        'name' => 'getOrderTotalCharge'
    ];

    public function type(): Type
    {
        return Type::string();
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $result = $this->_sql($root, $args, $context, $resolveInfo, $getSelectFields)->sum('orders.amount');

        return $result;
    }
}