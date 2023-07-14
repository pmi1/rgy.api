<?php
namespace App\GraphQL\Type;

use App\Role;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserRoleType extends GraphQLType
{
    protected $attributes = [
        'name' => 'UserRole',
        'description' => 'An user role',
        //'model'         => Role::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the user role'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the user role'
            ],
            'url' => [
                'type' => Type::int(),
                'description' => 'The url of the user role'
            ],
            'code' => [
                'type' => Type::string(),
                'description' => 'The code the user role'
            ]
        ];
    }

    protected function resolveIdField($root, $args)
    {
        return $root->cmf_role_id;
    }

    protected function resolveUrlField($root, $args)
    {
        return $root->cmf_url;
    }
}