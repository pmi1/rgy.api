<?php
namespace App\GraphQL\Type;

use App\OrderRentRefundReason;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderRentRefundReasonType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderRentRefundReason',
        'description' => 'A order rent refund reason type',
        'model' => OrderRentRefundReason::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the order rent refund reason',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the order rent refund reason'
            ],
        ];
    }
}