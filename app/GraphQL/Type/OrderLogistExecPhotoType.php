<?php
namespace App\GraphQL\Type;

use App\OrderLogistExecPhoto;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderLogistExecPhotoType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderLogistExecPhoto',
        'description' => 'A order Logist',
        'model' => OrderLogistExecPhoto::class,
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
            'item' => [
                'type' => GraphQL::type('Product'),
            ],
            'image' => [
                'type' => Type::string()
            ],
        ];
    }
}