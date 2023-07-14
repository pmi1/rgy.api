<?php
namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TaxType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Tax',
        'description' => 'A tax status'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the tax'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the tax'
            ],
            'sber' => [
                'type' => Type::string(),
                'description' => 'The sber id of the tax'
            ]
        ];
    }
}