<?php
namespace App\GraphQL\Query;

use App\PaymentVariant;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\GraphQL\RguysQuery;
use Rebing\GraphQL\Support\Facades\GraphQL;

class PaymentVariantQuery extends RguysQuery
{
    protected $attributes = [
        'name' => 'PaymentVariant'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('PaymentVariant'))));
    }

    public function resolve($root, $args)
    {
       return PaymentVariant::all();
    }
}