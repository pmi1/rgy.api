<?php
namespace App\GraphQL\Type;

use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use App\UserCashback;

class UserCashbackType extends GraphQLType
{
    protected $attributes = [
        'name' => 'UserCashback',
        'description' => 'A user cashback',
        'model' => UserCashback::class,
    ];

    public function fields(): array
    {

        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id',
            ],
            'user' => [
                'type' => GraphQL::type('User'),
                'description' => 'The user of cashback',
            ],
            'order' => [
                'type' => GraphQL::type('Order'),
                'description' => 'The order of cashback',
            ],
            'price' => [
                'type' => Type::string(),
                'description' => 'The price',
            ],
            'operation' => [
                'type' => GraphQL::type('CashbackOperation'),
                'description' => 'The operation',
            ],
            'paymentType' => [
                'type' => GraphQL::type('CashbackType'),
                'description' => 'The type',
            ],
            'date' => [
                'type' => Type::string(),
                'description' => 'The date insert',
                'alias' => 'cdate',
            ],
            'comment' => [
                'type' => Type::string(),
                'description' => 'The comment',
            ],
        ];
    }
}