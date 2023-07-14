<?php
namespace App\GraphQL\Query;

use App\PaymentType;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\GraphQL\RguysQuery;
use Rebing\GraphQL\Support\Facades\GraphQL;

class PaymentTypeQuery extends RguysQuery
{
    protected $attributes = [
        'name' => 'PaymentType'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('PaymentType'))));
    }

    public function resolve($root, $args)
    {
       return PaymentType::all();
    }
}