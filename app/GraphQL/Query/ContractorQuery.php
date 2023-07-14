<?php
namespace App\GraphQL\Query;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\User;
use App\Role;
use App\Contractor;
use App\GraphQL\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use App\GraphQL\RguysQuery;

class ContractorQuery extends RguysQuery
{
    use HasMiddleware;

    protected $attributes = [
        'name' => 'contractor'
    ];

    protected $middleware = [
        'auth:api'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type('Contractor'))));
    }

    public function resolve($root, $args)
    {
        $this->handleMiddleware();
        $user  = Auth::user();
        if (!$user->hasPid() && !$user->hasAnyRole(array_merge(Role::MANAGER_ROLE_CODES, Role::WAREHOUSE_CODES, Role::WORKSHOP, Role::CONTRACTOR_ROLE_CODES))) {
            throw new APIException('У вас нет прав для доступа к этой стринице');
        }
        return Contractor::where('cmf_site_id', config('common.siteId', 1))->get();
    }
}