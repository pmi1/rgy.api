<?php
namespace App\GraphQL\Type;

use App\OrderLogist;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderLogistType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderLogist',
        'description' => 'A order Logist',
        'model' => OrderLogist::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the order Logist',
            ],
            'order' => [
                'type' => GraphQL::type('Order'),
                'description' => 'The order',
            ],
            'type' => [
                'type' => GraphQL::type('OrderLogistType')
            ],
            'contractor' => [
                'type' => GraphQL::type('Contractor'),
            ],
            'helpers' => [
                'type' => Type::listOf(GraphQL::type('OrderLogistHelper')),
            ],
            'drivers' => [
                'type' => Type::listOf(GraphQL::type('OrderLogistDriver')),
            ],
            'exec' => [
                'type' => Type::listOf(GraphQL::type('OrderLogistExec')),
            ],
            'execPhoto' => [
                'type' => Type::listOf(GraphQL::type('OrderLogistExecPhoto')),
            ],
            'address' => [
                'type' => Type::string(),
            ],
            'duration' => [
                'type' => Type::string(),
            ],
            'comment' => [
                'type' => Type::string(),
            ],
            'comment_warehouse' => [
                'type' => Type::string(),
            ],
            'contact' => [
                'type' => Type::string(),
            ],
            'reason' => [
                'type' => Type::string(),
            ],
            'cdate' => [
                'type' => Type::string(),
            ],
            'edate' => [
                'type' => Type::string(),
            ],
            'finish' => [
                'type' => Type::string(),
            ],
            'status' => [
                'type' => Type::string(),
            ],
            'status_warehouse' => [
                'type' => Type::string(),
            ],
        ];
    }
}