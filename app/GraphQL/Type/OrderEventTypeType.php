<?php
namespace App\GraphQL\Type;

use App\OrderEventType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderEventTypeType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderEventType',
        'description' => 'A order event type',
        'model' => OrderEventType::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the order event type',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the order event type'
            ],
        ];
    }
}