<?php

namespace App\GraphQL\Mutation;

use CLosure;
use App\User;
use App\Role;
use App\Order;
use App\Contractor;
use App\LegalEntity;
use App\Exceptions\APIException;
use App\OrderLogist;
use GraphQL;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Auth;
use App\GraphQL\HasMiddleware;
use Carbon\Carbon;

class OrderLogistMutation extends Mutation
{
    protected $attributes = [
        'name' => 'OrderLogist'
    ];

    public function type(): Type
    {
        return GraphQL::type('OrderLogist');
    }

    public function args(): array
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::string()],
            'order' => ['name' => 'order', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
            'type' => ['name' => 'type', 'type' => Type::nonNull(GraphQL::type('OrderLogistType')), 'rules' => ['required']],
            'contractor' => ['name' => 'contractor', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
            'address' => ['name' => 'address', 'type' => Type::string()],
            'comment_warehouse' => ['name' => 'comment_warehouse', 'type' => Type::string()],
            'contact' => ['name' => 'contact', 'type' => Type::string()],
            'reason' => ['name' => 'reason', 'type' => Type::string()],
            'cdate' => ['name' => 'cdate', 'type' => Type::string()],
            'edate' => ['name' => 'edate', 'type' => Type::string()],
            'finish' => ['name' => 'finish', 'type' => Type::string()],
            'status' => ['name' => 'status', 'type' => Type::string()],
            'status_warehouse' => ['name' => 'status_warehouse', 'type' => Type::string()],
            'comment' => ['name' => 'comment', 'type' => Type::string()]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $byUser = Auth::user();

        $order = Order::find($args['order']);

        if(!$order) {

            throw new APIException('Order not found!');
        }

        if (!($byUser->hasAnyRole(array_merge(Role::MANAGER_ROLE_CODES)))) {

            throw new APIException('У вас нет прав для доступа к этой странице');
        }

        $contractor = Contractor::find($args['contractor']);

        if(!$contractor) {

            throw new APIException('Contractor not found!');
        }

        if (!(isset($args['id']) && ($p = OrderLogist::find($args['id'])))) {

            $p = new OrderLogist();
        }

        $fields = ['order' => 'orders_id', 'status' => 'status', 'type' => 'type', 'finish' => 'finish', 'reason' => 'reason'
            , 'contractor' => 'contractor_id', 'address' => 'address', 'cdate' => 'cdate', 'contact' => 'contact'
            , 'edate' => 'edate', 'comment' => 'comment', 'comment_warehouse' => 'comment_warehouse'];

        foreach ($fields as $key => $value) {

            if (isset($args[$key])) {

                $p[$value] = $args[$key];
            }
        }

        $p->save();

        return $p;
    }
}