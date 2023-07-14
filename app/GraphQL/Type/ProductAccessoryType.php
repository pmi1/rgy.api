<?php
namespace App\GraphQL\Type;

use App\ItemComplect;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProductAccessoryType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ProductAccessory',
        'description' => 'A link between product and accessory',
        'model' => ItemComplect::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of link',
                'alias' => 'item_complect_id'
            ],
            'quantity' => [
                'type' => Type::string(),
                'description' => 'The quantity of accessory',
            ],
            'product' => [
                'type' => GraphQL::type('Product'),
                'description' => 'The product',
            ],
            'accessory' => [
                'type' => GraphQL::type('Product'),
                'description' => 'The accessory',
            ],
        ];
    }
}