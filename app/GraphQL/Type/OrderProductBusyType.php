<?php
namespace App\GraphQL\Type;

use App\OrdersItem;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderProductBusyType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderProductBusy',
        'description' => 'Busy product for order',
    ];

    public function fields(): array
    {
        return [
            'amount' => [
                'type' => Type::string(),
                'description' => 'The quantity of product',
            ],
            'date' => [
                'type' => Type::string(),
                'description' => 'The date',
            ],
            'orders' => [
                'type' => Type::listOf(GraphQL::type('OrderProductBusyOrder')),
                'description' => 'The busy product',
                //'selectable' => false,
            ],
        ];
    }
}