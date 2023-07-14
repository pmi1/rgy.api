<?php
namespace App\GraphQL\Query;

use GraphQL\Type\Definition\Type;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\GraphQL\RguysQuery;
use Rebing\GraphQL\Support\Facades\GraphQL;

class PlatformsQuery extends RguysQuery
{
    protected $attributes = [
        'name' => 'platforms'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('Platform'))));
    }

    public function resolve($root, $args)
    {
        return  DB::table('user')
            ->whereNotNull('stand_id')
            ->get(['stand_id', 'theme', 'fonts']);
    }
}