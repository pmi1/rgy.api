<?php
namespace App\GraphQL\Query;

use App\CallRating;
use GraphQL\Type\Definition\Type;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\GraphQL\RguysQuery;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\Discounter;

class CallRatingsQuery extends RguysQuery
{
    protected $attributes = [
        'name' => 'callRatings'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('CallRating'))));
    }

    public function resolve($root, $args)
    {
       return CallRating::all();
    }
}