<?php
namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use App\OrdersPaymentStatus;

class OrderPaymentStatusType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderPaymentStatus',
        'description' => 'A order payment status',
        'model' => OrdersPaymentStatus::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the order payment status'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the order payment status'
            ],
            'ordering' => [
                'type' => Type::int(),
                'description' => 'The ordering of the order payment status'
            ]
        ];
    }
}