<?php
namespace App\GraphQL\Query;

use Closure;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Paginate;
use App\OrderLogist;
use App\Role;
use App\SearchFilters\OrderLogistSearch\OrderLogistSearch;
use App\SearchFilters\OrderLogistSearch\OrderLogistSearchResult;
use Illuminate\Support\Facades\Auth;
use App\GraphQL\HasMiddleware;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\SelectFields;

class OrderLogistQuery extends Query
{
    use HasMiddleware;

    protected $attributes = [
        'name' => 'orderLogist'
    ];

    protected $middleware = [
        'auth:api'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('OrderLogist');
    }

    public function args(): array
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::listOf(Type::string())],
            'type' => ['name' => 'type', 'type' => Type::listOf(GraphQL::type('OrderLogistType'))],
            'order' => ['name' => 'order', 'type' => Type::string()],
            'status' => ['name' => 'status', 'type' => Type::string()],
            'orderStatus' => ['name' => 'orderStatus', 'type' => Type::listOf(Type::string())],
            'from' => ['name' => 'from', 'type' => Type::string()],
            'to' => ['name' => 'to', 'type' => Type::string()],
            'page' => ['name' => 'page', 'type' => Type::string()],
            'limit' => ['name' => 'limit', 'type' => Type::string()]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $this->handleMiddleware();
        $user  = Auth::user();

        if (!$user->hasAnyRole(array_merge(Role::MANAGER_ROLE_CODES, Role::WORKSHOP))) {

            return response()->json(['success' => false], 200);
        }

        $fields = $getSelectFields();
        $sql = OrderLogistSearch::apply($args)->select($fields->getSelect())->with($fields->getRelations());

        $result = $sql->paginate(isset($args['limit']) ? $args['limit'] : env('PAGINATION_COUNT_PER_PAGE'), 
            ['*'], 'page', isset($args['page']) ? $args['page'] : 1);

        return $result;
    }
}