<?php
namespace App\GraphQL\Query;

use App\PaymentStatus;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\GraphQL\RguysQuery;
use Rebing\GraphQL\Support\Facades\GraphQL;

class PaymentStatusQuery extends RguysQuery
{
    protected $attributes = [
        'name' => 'PaymentStatus'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('PaymentStatus'))));
    }

    public function resolve($root, $args)
    {
       return PaymentStatus::all();
    }
}