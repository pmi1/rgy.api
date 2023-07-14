<?php
namespace App\GraphQL\Query;

use App\Car;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;

class CarQuery extends Query
{
    protected $attributes = [
        'name' => 'Car'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('Car'))));
    }

    public function resolve($root, $args)
    {
       return Car::all();
    }
}