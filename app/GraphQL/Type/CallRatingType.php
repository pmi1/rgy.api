<?php
namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CallRatingType extends GraphQLType
{
    protected $attributes = [
        'name' => 'CallRating',
        'description' => 'A call rating'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the call rating'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of call rating'
            ]
        ];
    }
}