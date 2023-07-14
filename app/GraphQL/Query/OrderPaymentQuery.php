<?php
namespace App\GraphQL\Query;

use Closure;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use App\GraphQL\RguysQuery;
use Rebing\GraphQL\Support\Paginate;
use App\OrderPayment;
use App\Role;
use App\SearchFilters\OrderPaymentSearch\OrderPaymentSearch;
use App\SearchFilters\OrderPaymentSearch\OrderPaymentSearchResult;
use Illuminate\Support\Facades\Auth;
use App\GraphQL\HasMiddleware;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\SelectFields;

class OrderPaymentQuery extends RguysQuery
{
    use HasMiddleware;

    protected $attributes = [
        'name' => 'OrderPayment'
    ];

    protected $middleware = [
        'auth:api'
    ];

    public function type(): Type
    {
        return GraphQL::paginate('OrderPayment');
    }

    public function args(): array
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::listOf(Type::string())],
            'status' => ['name' => 'status', 'type' => Type::listOf(Type::string())],
            'order' => ['name' => 'order', 'type' => Type::string()],
            'tax' => ['name' => 'tax', 'type' => Type::string()],
            'legalEntity' => ['name' => 'legalEntity', 'type' => Type::string()],
            'paymentType' => ['name' => 'paymentType', 'type' => Type::string()],
            'paymentVarinant' => ['name' => 'paymentVariant', 'type' => Type::string()],
            'page' => ['name' => 'page', 'type' => Type::string()],
            'limit' => ['name' => 'limit', 'type' => Type::string()],
            'sortBy' => ['name' => 'sortBy', 'type' => Type::string()],
            'sortDir' => ['name' => 'sortDir', 'type' => Type::string()]
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
        $sql = OrderPaymentSearch::apply($args)->select($fields->getSelect())->with($fields->getRelations())
            ->join('orders as o', 'o.orders_id', '=', 'orders_payment.orders_id', null)
            ->where('o.cmf_site_id', config('common.siteId', 1));

        $sortTypeMap = [
            'paymentStatus' =>'orders_payment.status',
            'tax' => 'orders_payment.tax_id',
            'id' => 'orders_payment.id',
            'legalEntity' => 'orders_payment.legal_entity_id',
            'amount' => 'orders_payment.amount',
            'paymentType' => 'orders_payment.payment_type',
            'paymentVariant' => 'orders_payment.payment_variant',
            'paymentDate' => 'orders_payment.payment_date',
            'order' => 'orders_payment.orders_id',
        ];

        if (isset($args['sortBy']) && $args['sortBy'] && isset($sortTypeMap[$args['sortBy']])) {

            $sql->orderBy($sortTypeMap[$args['sortBy']], isset($args['sortDir']) ? $args['sortDir'] : 'asc');
        }

        $result = $sql->paginate(isset($args['limit']) ? $args['limit'] : env('PAGINATION_COUNT_PER_PAGE'), 
            ['*'], 'page', isset($args['page']) ? $args['page'] : 1);

        return $result;
    }
}