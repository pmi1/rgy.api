<?php
namespace App\GraphQL\Query;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\GraphQL\RguysQuery;
use App\User;
use App\CashbackType;

class CashbackTypeQuery extends RguysQuery
{
    protected $attributes = [
        'name' => 'CashbackType'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('CashbackType'))));
    }

    public function resolve($root, $args)
    {
        return  CashbackType::all();
    }
}