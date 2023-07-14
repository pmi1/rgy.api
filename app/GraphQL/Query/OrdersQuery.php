<?php
namespace App\GraphQL\Query;

use Closure;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\GraphQL\RguysQuery;
use Rebing\GraphQL\Support\Paginate;
use App\Order;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Auth;
use App\GraphQL\HasMiddleware;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\SelectFields;

class OrdersQuery extends RguysQuery
{
    use HasMiddleware;

    protected $attributes = [
        'name' => 'getOrderList'
    ];

    protected $middleware = [
        'auth:api'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('Order');
    }

    public function args(): array
    {
        return [
            'dateType' => ['name' => 'dateType', 'type' => Type::string()],
            'from' => ['name' => 'from', 'type' => Type::string()],
            'to' => ['name' => 'to', 'type' => Type::string()],
            'id' => ['name' => 'id', 'type' => Type::listOf(Type::string())],
            'status' => ['name' => 'status', 'type' => Type::listOf(Type::string())],
            'manager' => ['name' => 'manager', 'type' => Type::listOf(Type::string())],
            'customer' => ['name' => 'customer', 'type' => Type::listOf(Type::string())],
            'pid' => ['name' => 'pid', 'type' => Type::listOf(Type::string())],
            'cmfSite' => ['name' => 'cmfSite', 'type' => Type::listOf(Type::string())],
            'cancelReason' => ['name' => 'cancelReason', 'type' => Type::listOf(Type::string())],
            'selfDelivery' => ['name' => 'selfDelivery', 'type' => Type::boolean()],
            'priceTo' => ['name' => 'priceTo', 'type' => Type::string()],
            'query' => ['name' => 'query', 'type' => Type::string()],
            'page' => ['name' => 'page', 'type' => Type::string()],
            'limit' => ['name' => 'limit', 'type' => Type::string()],
            'sortBy' => ['name' => 'sortBy', 'type' => Type::string()],
            'sortDir' => ['name' => 'sortDir', 'type' => Type::string()],
        ];
    }

    protected function _sql($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $this->handleMiddleware();
        $user = Auth::user();

        $fields = $getSelectFields();

        $sql = Order::select($fields->getSelect())->with($fields->getRelations());

        if (config('common.siteId', 1) != 1) {

            $sql->where('orders.cmf_site_id', config('common.siteId', 1));
        }

        if (!$user->hasPid() && !$user->hasAnyRole(Role::MANAGER_ROLE_CODES) && !$user->hasAnyRole(['cto', 'equipment'])) {

            $is = [$user->user_id];


            $users = User::where('main_user', $user->user_id)->get();

            foreach ($users as $value) {

                $is[] = $value->user_id;
            }

            if (isset($args['customer'])) {

                $args['customer'] = array_intersect($is, $args['customer']);

                if (empty($args['customer'])) {

                    $args['customer'] = [-1];
                }

            } elseif ($user->hasAnyRole(Role::CONTRACTOR_ROLE_CODES)) {

                $sql->whereRaw('(orders.user_id IN ('.implode(',', $is).') OR orders.orders_status_id IN (38,39))');
            } else {

                $args['customer'] = $is;
            }
        }

        $sortTypeMap = [
            'status' =>'orders.orders_status_id',
            'installDate' => 'installdate',
            'id' => 'orders.orders_id',
            'ordered' => 'orders.date_insert',
            'uninstallDate' => 'enddate',
            'price' => 'orders.amount',
        ];

        if (isset($args['sortBy']) && $args['sortBy'] && isset($sortTypeMap[$args['sortBy']])) {

            $sql->orderBy($sortTypeMap[$args['sortBy']], isset($args['sortDir']) ? $args['sortDir'] : 'asc');
        }

        if (isset($args['query']) && !empty($args['query'])) {

            $query = $args['query'];
            $sql->leftJoin('user as c', 'c.user_id', '=', 'orders.user_id')
                ->whereRaw(
                "(c.name like '%$query%' or c.secondname like '%$query%' or c.lastname like '%$query%' or c.email like '%$query%' or c.phone like '%$query%' or orders.orders_id = '$query')");
        } else {

            if (isset($args['dateType']) && in_array($args['dateType'], ['0', '1', '2'])) {

                $field = ($args['dateType'] === '1' ? 'STR_TO_DATE(orders.installdate, \'%Y-%m-%d\')' : ($args['dateType'] === '2' ? 'STR_TO_DATE(orders.enddate, \'%Y-%m-%d\')' : 'orders.date_insert'));

                if (isset($args['from']) && $args['from']) {
                    $sql->whereRaw($field.' >= ?', [date('Y-m-d H:i', strtotime($args['from']))]);
                }

                if (isset($args['to']) && $args['to']) {
                    $sql->whereRaw($field.' <= ?', [date('Y-m-d H:i', strtotime($args['to']))]);
                }
            }

            if (isset($args['priceTo']) && $args['priceTo']) {

                $sql->whereRaw("(CAST(orders.amount AS DECIMAL(10,6)) <= ".intval($args['priceTo']).")");
            }

            if (isset($args['selfDelivery']) && $args['selfDelivery']) {

                $sql->where('orders.is_pickup', $args['selfDelivery']);
            }

            $options = ['customer' => 'user_id', 'manager' => 'operator_id', 'status' => 'orders_status_id'
                , 'id' => 'orders_id', 'pid' => 'stage_id', 'cmfSite' => 'cmf_site_id', 'cancelReason' => 'order_cancel_reason_id'];

            foreach ($options as $key => $value) {

                if (isset($args[$key]) && $args[$key]) {

                    $sql->whereIn('orders.'.$value, $args[$key]);
                }
            }
        }

        return $sql;
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $result = $this->_sql($root, $args, $context, $resolveInfo, $getSelectFields)->paginate(isset($args['page']) ? 
            (isset($args['limit']) ? $args['limit'] : env('PAGINATION_COUNT_PER_PAGE')) : 10000000, 
            ['*'], 'page', isset($args['page']) ? $args['page'] : 1);

        return $result;
    }

}