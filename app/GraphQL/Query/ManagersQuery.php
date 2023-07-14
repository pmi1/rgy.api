<?php
namespace App\GraphQL\Query;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Auth;
use App\GraphQL\RguysQuery;
use App\Exceptions\APIException;

class ManagersQuery extends RguysQuery
{
    protected $attributes = [
        'name' => 'managers'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('User'))));
    }

    public function resolve($root, $args)
    {
        $user  = Auth::user();
        if (!$user->hasPid() && !$user->hasAnyRole(array_merge(Role::MANAGER_ROLE_CODES, Role::WAREHOUSE_CODES, Role::WORKSHOP, Role::CONTRACTOR_ROLE_CODES))) {
            throw new APIException('У вас нет прав для доступа к этой стринице');
        }
        return User::whereHas('roles', function ($query){
            $query->whereIn('code', Role::MANAGER_ROLE_CODES);
        })->get();
    }
}