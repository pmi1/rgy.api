<?php
namespace App\GraphQL\Type;

use App\OrderLogistExec;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderLogistExecType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderLogistExec',
        'description' => 'A order Logist',
        'model' => OrderLogistExec::class,
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
            'item' => [
                'type' => GraphQL::type('Product'),
            ],
            'parent' => [
                'type' => GraphQL::type('Product'),
            ],
            'quantity' => [
                'type' => Type::string()
            ],
            'item_id' => [
                'type' => Type::string()
            ],
            'parent_id' => [
                'type' => Type::string()
            ],
            'damaged' => [
                'type' => Type::string()
            ],
            'cdate' => [
                'type' => Type::string(),
            ],
            'reason' => [
                'type' => Type::string(),
            ],
        ];
    }
}