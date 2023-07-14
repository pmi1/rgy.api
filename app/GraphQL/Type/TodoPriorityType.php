<?php
namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TodoPriorityType extends GraphQLType
{
    protected $attributes = [
        'name' => 'TodoPriority',
        'description' => 'A todo priority'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the todo priority'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the todo priority'
            ],
            'color' => [
                'type' => Type::string(),
                'description' => 'The color of the todo priority'
            ]
        ];
    }
}