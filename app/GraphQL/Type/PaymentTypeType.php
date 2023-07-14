<?php
namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PaymentTypeType extends GraphQLType
{
    protected $attributes = [
        'name' => 'PaymentType',
        'description' => 'A Payment Type'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the PaymentType'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the PaymentType'
            ]
        ];
    }
}