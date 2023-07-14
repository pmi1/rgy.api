<?php
namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TodoStatusType extends GraphQLType
{
    protected $attributes = [
        'name' => 'TodoStatus',
        'description' => 'A todo status'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the todo status'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the todo status'
            ],
            'color' => [
                'type' => Type::string(),
                'description' => 'The color of the todo status'
            ]
        ];
    }
}