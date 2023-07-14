<?php
namespace App\GraphQL\Query;

use Closure;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Paginate;
use App\OrderComplect;
use App\Role;
use App\SearchFilters\OrderComplectSearch\OrderComplectSearch;
use App\SearchFilters\OrderComplectSearch\OrderComplectSearchResult;
use Illuminate\Support\Facades\Auth;
use App\GraphQL\HasMiddleware;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\SelectFields;

class OrderComplectQuery extends Query
{
    use HasMiddleware;

    protected $attributes = [
        'name' => 'OrderComplect'
    ];

    protected $middleware = [
        'auth:api'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('OrderComplect');
    }

    public function args(): array
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::listOf(Type::string())],
            'item' => ['name' => 'item', 'type' => Type::listOf(Type::string())],
            'order' => ['name' => 'order', 'type' => Type::string()],
            'contractor' => ['name' => 'contractor', 'type' => Type::string()],
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
        $sql = OrderComplectSearch::apply($args)->select($fields->getSelect())->with($fields->getRelations());

        $result = $sql->paginate(isset($args['limit']) ? $args['limit'] : env('PAGINATION_COUNT_PER_PAGE'), 
            ['*'], 'page', isset($args['page']) ? $args['page'] : 1);

        return $result;
    }
}