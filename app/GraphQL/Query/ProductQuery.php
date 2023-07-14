<?php
namespace App\GraphQL\Query;

use Closure;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\GraphQL\RguysQuery;
use Rebing\GraphQL\Support\Paginate;
use App\Product;
use App\Role;
use App\SearchFilters\ItemSearch\ItemSearch;
use App\SearchFilters\ItemSearch\ItemSearchResult;
use Illuminate\Support\Facades\Auth;
use App\GraphQL\HasMiddleware;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\SelectFields;

class ProductQuery extends RguysQuery
{
    use HasMiddleware;

    protected $attributes = [
        'name' => 'Product'
    ];

    protected $middleware = [
        'auth:api'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('Product');
    }

    public function args(): array
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::listOf(Type::string())],
            'status' => ['name' => 'status', 'type' => Type::listOf(Type::string())],
            'priceTo' => ['name' => 'priceTo', 'type' => Type::string()],
            'query' => ['name' => 'query', 'type' => Type::string()],
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
        $sql = ItemSearch::apply($args)->select($fields->getSelect())->with($fields->getRelations())
            ->where('item.cmf_site_id', config('common.siteId', 1));

        $result = $sql->paginate(isset($args['page']) ? 
            (isset($args['limit']) ? $args['limit'] : env('PAGINATION_COUNT_PER_PAGE')) : 10000000, 
            ['*'], 'page', isset($args['page']) ? $args['page'] : 1);

        return $result;
    }
}