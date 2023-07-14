<?php
namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class DiscounterType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Discounter',
        'description' => 'A discounter'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the order cancel reason',
                'alias' => 'discounter_id'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the order cancel'
            ],
            'percentDiscounter' => [
                'type' => Type::string(),
                'description' => 'The percentDiscounter of the order cancel'
            ],
            'description' => [
                'type' => Type::string(),
                'description' => 'The description of the order cancel'
            ],
            'status' => [
                'type' => Type::int(),
                'description' => 'The status of the order cancel'
            ]
        ];
    }

    protected function resolveIdField($root, $args)
    {
        return strtolower($root->discounter_id);
    }

    protected function resolvePercentDiscounterField($root, $args)
    {
        return strtolower($root->percent_discounter);
    }
}