<?php
namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PlatformType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Platform',
        'description' => 'A platform'
    ];

    public function fields(): array
    {
        return [
            'theme' => [
                'type' => Type::string(),
                'description' => 'The theme'
            ],
            'fonts' => [
                'type' => Type::string(),
                'description' => 'The fonts'
            ],
            'standID' => [
                'type' => Type::string(),
                'description' => 'The stand id'
            ],
        ];
    }

    protected function resolveStandIDField($root, $args)
    {
        return $root->stand_id;
    }
}