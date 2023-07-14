<?php
namespace App\GraphQL\Type;

use App\OrderServiceRefundReason;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderServiceRefundReasonType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderServiceRefundReason',
        'description' => 'A order service refund reason type',
        'model' => OrderServiceRefundReason::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the order service refund reason',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the order service refund reason'
            ],
        ];
    }
}