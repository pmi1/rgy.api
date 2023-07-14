<?php
namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PaymentVariantType extends GraphQLType
{
    protected $attributes = [
        'name' => 'PaymentVariant',
        'description' => 'A Payment Variant'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the PaymentVariant'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the PaymentVariant'
            ]
        ];
    }
}