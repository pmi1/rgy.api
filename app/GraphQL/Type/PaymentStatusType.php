<?php
namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PaymentStatusType extends GraphQLType
{
    protected $attributes = [
        'name' => 'PaymentStatus',
        'description' => 'A Payment Status'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the PaymentStatus'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the PaymentStatus'
            ]
        ];
    }
}