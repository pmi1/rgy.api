<?php
namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CallTypeType extends GraphQLType
{
    protected $attributes = [
        'name' => 'CallType',
        'description' => 'A call type'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the call type'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of call type'
            ]
        ];
    }
}