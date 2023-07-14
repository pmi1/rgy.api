<?php
namespace App\GraphQL\Type;

use App\OrderStatus;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderStatusType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderStatus',
        'description' => 'A order status',
        'model' => OrderStatus::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the order status',
                'alias' => 'orders_status_id',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the order status'
            ],
            'color' => [
                'type' => Type::string(),
                'description' => 'The color of the order status'
            ],
            'description' => [
                'type' => Type::string(),
                'description' => 'The description of the order status'
            ],
            'status' => [
                'type' => Type::int(),
                'description' => 'The status of the order status'
            ],
        ];
    }
}