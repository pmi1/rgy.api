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

class SberPaymentQuery extends RguysQuery
{
    use HasMiddleware;

    protected $attributes = [
        'name' => 'SberPayment'
    ];

    protected $middleware = [
        'auth:api'
    ];

    public function type(): Type
    {
        return Type::string();
    }

    public function args(): array
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::string()],
            'returnUrl' => ['name' => 'returnUrl', 'type' => Type::string()],
            'failUrl' => ['name' => 'failUrl', 'type' => Type::string()]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $this->handleMiddleware();

        if (!(isset($args['id']) && ($p = OrderPayment::find($args['id'])))) {

            return response()->json(['success' => false], 200);
        }

        return $p->getUrlAttribute(isset($args['returnUrl']) ? $args['returnUrl'] : '', isset($args['failUrl']) ? $args['failUrl'] : '');
    }
}