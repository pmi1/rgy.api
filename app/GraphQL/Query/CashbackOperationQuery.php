<?php
namespace App\GraphQL\Query;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\GraphQL\RguysQuery;
use App\User;
use App\CashbackOperation;

class CashbackOperationQuery extends RguysQuery
{
    protected $attributes = [
        'name' => 'CashbackOperation'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('CashbackOperation'))));
    }

    public function resolve($root, $args)
    {
        return  CashbackOperation::all();
    }
}