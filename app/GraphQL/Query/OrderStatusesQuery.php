<?php
namespace App\GraphQL\Query;

use GraphQL\Type\Definition\Type;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\GraphQL\RguysQuery;
use Rebing\GraphQL\Support\Facades\GraphQL;

class OrderStatusesQuery extends RguysQuery
{
    protected $attributes = [
        'name' => 'orderStatuses'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('OrderStatus'))));
    }

    public function resolve($root, $args)
    {
        return DB::table('orders_status')
            ->select([
                'orders_status_id',
                'name',
                'status',
                'color',
                'description',
            ])
            ->orderBy('ordering')
            ->get();
    }
}