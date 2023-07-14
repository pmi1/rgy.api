<?php
namespace App\GraphQL\Query;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\GraphQL\RguysQuery;
use App\User;
use App\UserStatus;

class UserStatusesQuery extends RguysQuery
{
    protected $attributes = [
        'name' => 'userStatuses'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('UserStatus'))));
    }

    public function resolve($root, $args)
    {
        return  UserStatus::all();
    }
}