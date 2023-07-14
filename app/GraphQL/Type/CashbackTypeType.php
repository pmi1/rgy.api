<?php
namespace App\GraphQL\Type;

use App\CashbackType;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CashbackTypeType extends GraphQLType
{
    protected $attributes = [
        'name' => 'CashbackType',
        'description' => 'A cashback type',
        'model' => CashbackType::class,
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