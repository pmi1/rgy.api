<?php
namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TodoStateType extends GraphQLType
{
    protected $attributes = [
        'name' => 'TodoState',
        'description' => 'A todo state'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the todo state'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The description of the todo state'
            ],
            'color' => [
                'type' => Type::string(),
                'description' => 'The color of the todo state'
            ]
        ];
    }
}