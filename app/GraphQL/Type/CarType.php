<?php
namespace App\GraphQL\Type;

use App\Car;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CarType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Car',
        'model' => Car::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'alias' => 'car_id'
            ],
            'name' => [
                'type' => Type::string(),
            ],
            'color' => [
                'type' => Type::string(),
            ],
            'nomer' => [
                'type' => Type::string(),
            ],
            'url' => [
                'type' => Type::string(),
            ],
        ];
    }
}