<?php
namespace App\GraphQL\Type;

use App\OrderComplect;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class OrderComplectType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderComplect',
        'description' => 'A order complect',
        'model' => OrderComplect::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the order complect',
            ],
            'order' => [
                'type' => GraphQL::type('Order'),
                'description' => 'The order of order complect'
            ],
            'item' => [
                'type' => GraphQL::type('Product'),
                'description' => 'The item of order complect'
            ],
            'contractor' => [
                'type' => GraphQL::type('Contractor'),
                'description' => 'The order of order complect'
            ],
            'quantity' => [
                'type' => Type::string(),
            ],
            'quantityLost' => [
                'type' => Type::string(),
                'alias' => 'quantity_lost',
            ],
            'quantityRepair' => [
                'type' => Type::string(),
                'alias' => 'quantity_repair',
            ],
            'quantityBack' => [
                'type' => Type::string(),
                'alias' => 'quantity_back',
            ],
            'isCollected' => [
                'type' => Type::string(),
                'alias' => 'is_collected',
            ],
            'condition' => [
                'type' => Type::string(),
                'alias' => 'condition',
            ],
        ];
    }
}