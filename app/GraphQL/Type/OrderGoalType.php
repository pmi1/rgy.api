<?php
namespace App\GraphQL\Type;

use App\OrderGoal;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderGoalType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderGoal',
        'description' => 'A order goal',
        'model' => OrderGoal::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the order goal',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the order goal'
            ],
        ];
    }
}