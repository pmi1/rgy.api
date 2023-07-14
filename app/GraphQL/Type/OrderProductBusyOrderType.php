<?php
namespace App\GraphQL\Type;

use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderProductBusyOrderType extends GraphQLType
{
    protected $attributes = [
        'name' => 'OrderProductBusyOrder',
        'description' => 'Busy product for order',
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the order',
            ],
            'status' => [
                'type' => Type::string(),
                'description' => 'The status of order',
            ],
        ];
    }
}