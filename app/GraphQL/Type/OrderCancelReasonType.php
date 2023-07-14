<?php
namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderCancelReasonType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderCancelReason',
        'description' => 'A order cancel reason'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the order cancel reason'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the order cancel reason'
            ]
        ];
    }
}