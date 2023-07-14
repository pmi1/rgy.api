<?php
namespace App\GraphQL\Type;

use App\Item;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProductType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Product',
        'description' => 'A product',
        'model' => Item::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of product',
                'alias' => 'item_id',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of product',
                'alias' => 'itemtype',
            ],
            'type' => [
                'type' => Type::string(),
                'description' => 'The type of product',
                'alias' => 'typename',
            ],
            'art' => [
                'type' => Type::string(),
                'description' => 'The article of product',
                'alias' => 'article',
            ],
            'price' => [
                'type' => Type::string(),
                'description' => 'The product price'
            ],
            'available' => [
                'type' => Type::string(),
                'description' => 'The product available',
                'alias' => 'availablecount',
            ],
            'pledge' => [
                'type' => Type::string(),
                'description' => 'The product pledge',
            ],
            'minimalHours' => [
                'type' => Type::string(),
                'description' => 'The product min hours',
                'alias' => 'min_hours'
            ],
            'secondDayDiscount' => [
                'type' => Type::string(),
                'description' => 'The product discount',
                'alias' => 'discount_2_day'
            ],
            'rentType' => [
                'type' => Type::string(),
                'description' => 'The product rent type',
                'alias' => 'arendatype',
            ],
            'repair' => [
                'type' => Type::string(),
                'description' => 'The product repair',
            ],
            'oneCar' => [
                'type' => Type::string(),
                'description' => 'The product oneCar',
                'alias' => 'one_car',
            ],
            'stockRack' => [
                'type' => Type::string(),
                'description' => 'The product stockRack',
                'alias' => 'stelag',
            ],
            'accessories' => [
                'type' => Type::listOf(GraphQL::type('ProductAccessory')),
                'description' => 'The accessories of product',
            ],
            'itemContractors' => [
                'type' => Type::listOf(GraphQL::type('ProductContractor')),
            ],
            'userCategories' => [
                'type' => Type::listOf(GraphQL::type('ProductUserCategory')),
                'description' => 'The user category',
            ],
            'image' => [
                'type' => Type::string(),
                'description' => 'The product image',
                'selectable' => false,
            ],
            'secondDayDiscount' => [
                'type' => Type::string(),
                'description' => 'The cashback of product',
                'alias' => 'discount_2_day',
            ],
            'cashback' => [
                'type' => Type::string(),
                'description' => 'The cashback of product',
            ],
            'payfromCashback' => [
                'type' => Type::string(),
                'description' => 'The payfromCashback of product',
                'alias' => 'payfrom_cashback',
            ],
            'showCategoryDiscount' => [
                'type' => Type::boolean(),
                'description' => 'The show category discount of product',
                'alias' => 'use_discount',
            ],
            'tax' => [
                'type' => GraphQL::type('Tax'),
                'description' => 'The tax system',
            ],
        ];
    }
}