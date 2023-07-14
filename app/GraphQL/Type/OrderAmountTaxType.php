<?php
namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderAmountTaxType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderAmountTax',
        'description' => 'Order Amount Tax'
    ];

    public function fields(): array
    {
        return [
            'tax' => [
                'type' => Type::nonNull(GraphQL::type('Tax')),
                'description' => 'The id of the tax'
            ],
            'amount' => [
                'type' => Type::string(),
                'description' => 'The amount'
            ],
            'count' => [
                'type' => Type::string(),
                'description' => 'The count products'
            ]
        ];
    }
}