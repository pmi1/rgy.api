<?php
namespace App\GraphQL\Query;

use App\OrderGoal;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\GraphQL\RguysQuery;
use Rebing\GraphQL\Support\Facades\GraphQL;

class OrderGoalQuery extends RguysQuery
{
    protected $attributes = [
        'name' => 'orderGoal'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('OrderGoal'))));
    }

    public function resolve($root, $args)
    {
        return OrderGoal::all();
    }
}