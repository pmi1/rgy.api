<?php
namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TodoTypeType extends GraphQLType
{
    protected $attributes = [
        'name' => 'TodoType',
        'description' => 'A todo type'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the todo type'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the todo type'
            ]
        ];
    }
}