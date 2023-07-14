<?php
namespace App\GraphQL\Type;

use App\OrderLogistDriver;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderLogistDriverType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderLogistDriver',
        'description' => 'A order Logist',
        'model' => OrderLogistDriver::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
            ],
            'orderLogist' => [
                'type' => GraphQL::type('OrderLogist'),
            ],
            'user' => [
                'type' => GraphQL::type('User'),
            ],
            'carLink' => [
                'type' => GraphQL::type('Car'),
            ],
            'car' => [
                'type' => Type::string(),
            ],
            'driver' => [
                'type' => Type::string(),
            ],
            'main' => [
                'type' => Type::string(),
            ],
            'tripCount' => [
                'type' => Type::string(),
                'alias' => 'trip_count'
            ],
        ];
    }
}