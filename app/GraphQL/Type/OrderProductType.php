<?php
namespace App\GraphQL\Type;

use App\OrdersItem;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderProductType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderProduct',
        'description' => 'A link between product and order',
        'model' => OrdersItem::class,
    ];

    public function fields(): array
    {
        return [
            'quantity' => [
                'type' => Type::string(),
                'description' => 'The quantity of product',
            ],
            'price' => [
                'type' => Type::string(),
                'description' => 'The price of product',
            ],
            'fullPrice' => [
                'type' => Type::string(),
                'description' => 'The price full of product',
                'alias' => 'full_price',
            ],
            'showCategoryDiscount' => [
                'type' => Type::boolean(),
                'description' => 'Show category discount of product',
                'alias' => 'use_discount',
            ],
            'secondDayDiscount' => [
                'type' => Type::string(),
                'description' => 'The discount on second day of product',
                'alias' => 'discount_2_day',
            ],
            'additionalDiscount' => [
                'type' => Type::string(),
                'description' => 'The additinal discount of product',
                'alias' => 'additional_discount',
            ],
            'customDiscount' => [
                'type' => Type::string(),
                'description' => 'The discount of product',
                'alias' => 'discount_percent_from_manager',
            ],
            'product' => [
                'type' => GraphQL::type('Product'),
                'description' => 'The product',
            ],
            'coefficient' => [
                'type' => Type::string(),
                'description' => 'The coefficient of product',
            ],
            'busy' => [
                'type' => Type::listOf(GraphQL::type('OrderProductBusy')),
                'description' => 'The busy product',
                'selectable' => false,
            ],
        ];
    }
}