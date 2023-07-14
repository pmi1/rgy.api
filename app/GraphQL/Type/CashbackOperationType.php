<?php
namespace App\GraphQL\Type;

use App\CashbackOperation;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CashbackOperationType extends GraphQLType
{
    protected $attributes = [
        'name' => 'CashbackOperation',
        'description' => 'A cashback operations',
        'model' => CashbackOperation::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the order status',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name'
            ],
        ];
    }
}