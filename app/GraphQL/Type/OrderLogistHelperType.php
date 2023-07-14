<?php
namespace App\GraphQL\Type;

use App\OrderLogistHelper;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderLogistHelperType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderLogistHelper',
        'description' => 'A order Logist',
        'model' => OrderLogistHelper::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
            ],
            'orderLogist' => [
                'type' => GraphQL::type('OrderLogist'),
            ],
            'user' => [
                'type' => GraphQL::type('User'),
            ],
            'helper' => [
                'type' => Type::string(),
                'alias' => 'name'
            ],
            'main' => [
                'type' => Type::string(),
            ],
        ];
    }
}