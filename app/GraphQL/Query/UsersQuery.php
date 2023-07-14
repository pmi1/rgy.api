<?php
namespace App\GraphQL\Query;

use Closure;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\GraphQL\RguysQuery;
use App\User;
use App\Role;
use Rebing\GraphQL\Support\Paginate;
use App\SearchFilters\UserSearch\UserSearch;
use App\SearchFilters\UserSearch\UserSearchResult;
use App\GraphQL\HasMiddleware;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\SelectFields;
use Illuminate\Support\Facades\Auth;

class UsersQuery extends RguysQuery
{
    protected $attributes = [
        'name' => 'users'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('User');
    }

    public function args(): array
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::string()],
            'query' => ['name' => 'query', 'type' => Type::string()],
            'email' => ['name' => 'email', 'type' => Type::string()],
            'phone' => ['name' => 'phone', 'type' => Type::string()],
            'discounter' => ['name' => 'discounter', 'type' => Type::string()],
            'cashback' => ['name' => 'cashback', 'type' => Type::string()],
            'companyName' => ['name' => 'companyName', 'type' => Type::string()],
            'userCategory' => ['name' => 'userCategory', 'type' => Type::string()],
            'page' => ['name' => 'page', 'type' => Type::string()],
            'limit' => ['name' => 'limit', 'type' => Type::string()],
            'sortBy' => ['name' => 'sortBy', 'type' => Type::string()],
            'sortDir' => ['name' => 'sortDir', 'type' => Type::string()],
        ];
    }


    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $user  = Auth::user();

        if (!$user->hasAnyRole(array_merge(Role::MANAGER_ROLE_CODES))) {

            return response()->json(['success' => false], 200);
        }

        $fields = $getSelectFields();
        $sql = UserSearch::apply($args)->select($fields->getSelect())->with($fields->getRelations())
            ->leftJoin('orders as o', 'o.user_id', 'user.user_id')
            ->groupBy("user.user_id")
            ->whereRaw("(o.cmf_site_id=? or o.user_id is null)", config('common.siteId', 1));

        $sortTypeMap = [
            'id' =>'user.user_id',
            'cashback' =>'user.cashback',
            'discounter' => 'd.name',
            'userCategory' => 'uc.name',
            'company' => 'user.company',
            'gender' => 'user.pol',
            'companyName' => 'user.companyname',
            'doneOrderCount' => 'user.doneOrderCount',
            'doneOrderSum' => 'user.doneOrderSum',
        ];

        if (isset($args['sortBy']) && $args['sortBy'] === 'discounter') {
            $sql->leftJoin('discounter as d', 'd.discounter_id', 'user.discounter_id');
        }

        if (isset($args['sortBy']) && $args['sortBy'] === 'userCategory') {
            $sql->leftJoin('user_category as uc', 'uc.user_category_id', 'user.user_category_id');
        }

        if (isset($args['sortBy']) && $args['sortBy'] && isset($sortTypeMap[$args['sortBy']])) {

            $sql->orderBy($sortTypeMap[$args['sortBy']], isset($args['sortDir']) ? $args['sortDir'] : 'asc');
        }

        $result = $sql->paginate(isset($args['page']) ? 
            (isset($args['limit']) ? $args['limit'] : env('PAGINATION_COUNT_PER_PAGE')) : 10000000, 
            ['*'], 'page', isset($args['page']) ? $args['page'] : 1);

        return $result;
    }
}