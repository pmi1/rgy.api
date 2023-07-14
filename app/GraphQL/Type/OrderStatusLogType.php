<?php
namespace App\GraphQL\Type;

use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use App\OrderStatusLog;

class OrderStatusLogType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderStatusLog',
        'description' => 'A order status log',
        'model' => OrderStatusLog::class,
    ];

    public function fields(): array
    {

        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the log',
                'alias' => 'orders_id'
            ],
            'status' => [
                'type' => Type::string(),
                'description' => 'The status of log',
                'alias' => 'status_id',
            ],
            'user' => [
                'type' => GraphQL::type('User'),
                'description' => 'The user of log',
            ],
            'date' => [
                'type' => Type::string(),
                'description' => 'The date insert of log',
                'alias' => 'cdate',
            ],
        ];
    }
}