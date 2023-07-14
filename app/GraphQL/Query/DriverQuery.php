<?php
namespace App\GraphQL\Query;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\User;
use App\Role;
use App\GraphQL\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Query;

class DriverQuery extends Query
{
    use HasMiddleware;

    protected $attributes = [
        'name' => 'driver'
    ];

    protected $middleware = [
        'auth:api'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('User'))));
    }

    public function resolve($root, $args)
    {
        $this->handleMiddleware();
        $user  = Auth::user();
        if (!$user->hasPid() && !$user->hasAnyRole(array_merge(Role::MANAGER_ROLE_CODES, Role::WAREHOUSE_CODES, Role::WORKSHOP, Role::CONTRACTOR_ROLE_CODES))) {
            throw new APIException('У вас нет прав для доступа к этой стринице');
        }
        return User::whereHas('roles', function ($query){
            $query->whereIn('code', Role::DRIVER);
        })->get();
    }
}