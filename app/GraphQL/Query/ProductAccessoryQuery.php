<?php
namespace App\GraphQL\Query;

use GraphQL\Type\Definition\Type;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\GraphQL\RguysQuery;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\ItemComplect;

class ProductAccessoryQuery extends RguysQuery
{
    protected $attributes = [
        'name' => 'Product Accessory'
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('ProductAccessory'));
    }

    public function resolve($root, $args)
    {
       return ItemComplect::all();
    }
}