<?php
namespace App\GraphQL\Type;

use App\OrderPayment;
use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderPaymentType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderPayment',
        'description' => 'A OrderPayment status',
        'model' => OrderPayment::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the OrderPayment'
            ],
            'amount' => [
                'type' => Type::string(),
                'description' => 'The amount of the OrderPayment'
            ],
            'paymentStatus' => [
                'type' => GraphQL::type('PaymentStatus'),
                'description' => 'The status of the OrderPayment'
            ],
            'paymentType' => [
                'type' => GraphQL::type('PaymentType'),
                'description' => 'The PaymentType of the OrderPayment',
            ],
            'paymentDate' => [
                'type' => Type::string(),
                'description' => 'The PaymentDate of the OrderPayment',
                'alias' => 'payment_date'
            ],
            'tax' => [
                'type' => GraphQL::type('Tax'),
                'description' => 'The tax',
            ],
            'order' => [
                'type' => GraphQL::type('Order'),
                'description' => 'The order',
            ],
            'user' => [
                'type' => GraphQL::type('User'),
                'description' => 'The user',
            ],
            'paymentAt' => [
                'type' => Type::string(),
                'description' => 'The payment at',
                'alias' => 'payment_at'
            ],
            'legalEntity' => [
                'type' => GraphQL::type('LegalEntity'),
                'description' => 'The legalEntity',
            ],
            'paymentVariant' => [
                'type' => GraphQL::type('PaymentVariant'),
                'description' => 'The paymentVariant',
            ],
            'url' => [
                'type' => Type::string(),
                'description' => 'The payment url',
                'selectable' => false,
            ],
        ];
    }
}