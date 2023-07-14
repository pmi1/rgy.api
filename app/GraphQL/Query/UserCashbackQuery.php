<?php
namespace App\GraphQL\Query;

use Closure;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\GraphQL\RguysQuery;
use Rebing\GraphQL\Support\Paginate;
use App\UserCashback;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Auth;
use App\GraphQL\HasMiddleware;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\SelectFields;

class UserCashbackQuery extends RguysQuery
{
    use HasMiddleware;

    protected $attributes = [
        'name' => 'userCashback'
    ];

    protected $middleware = [
        'auth:api'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('UserCashback');
    }

    public function args(): array
    {
        return [
            'user' => ['name' => 'user', 'type' => Type::listOf(Type::string())],
            'cmfSite' => ['name' => 'cmfSite', 'type' => Type::string()],
            'page' => ['name' => 'page', 'type' => Type::string()],
            'limit' => ['name' => 'limit', 'type' => Type::string()],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $this->handleMiddleware();
        $user = Auth::user();

        if (!$user->hasAnyRole(array_merge(Role::MANAGER_ROLE_CODES))) {

            throw new APIException('У вас нет прав для доступа к этой странице');
        }

        $fields = $getSelectFields();

        $sql = UserCashback::select($fields->getSelect())->with($fields->getRelations());

        if (isset($args['user']) && $args['user']) {

            $sql->whereIn('user_cashback.user_id', $args['user']);
        }

        if (isset($args['cmfSite']) && $args['cmfSite']) {

            $sql->leftJoin('orders as o',function($join) {
                     $join->on('o.orders_id', '=', 'user_cashback.order_id');
                 })
                 ->where(function ($query) use($args) {
                    $query->where('o.cmf_site_id', $args['cmfSite']);

                    if ($args['cmfSite'] == 1) {

                        $query->orWhereNull('user_cashback.order_id');
                    }
                 });
        }

        $result = $sql->paginate(isset($args['page']) ? 
            (isset($args['limit']) ? $args['limit'] : env('PAGINATION_COUNT_PER_PAGE')) : 10000000, 
            ['*'], 'page', isset($args['page']) ? $args['page'] : 1);

        return $result;
    }

}