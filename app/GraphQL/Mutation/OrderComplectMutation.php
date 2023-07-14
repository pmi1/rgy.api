<?php

namespace App\GraphQL\Mutation;

use CLosure;
use App\User;
use App\Role;
use App\Order;
use App\Item;
use App\Contractor;
use App\Exceptions\APIException;
use App\OrderComplect;
use GraphQL;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Auth;
use App\GraphQL\HasMiddleware;
use Carbon\Carbon;

class OrderComplectMutation extends Mutation
{
    protected $attributes = [
        'name' => 'OrderComplect'
    ];

    public function type(): Type
    {
        return GraphQL::type('OrderComplect');
    }

    public function args(): array
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::string()],
            'order' => ['name' => 'order', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
            'item' => ['name' => 'item', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
            'contractor' => ['name' => 'contractor', 'type' => Type::string()],
            'quantity' => ['name' => 'quantity', 'type' => Type::string()],
            'quantityLost' => ['name' => 'quantityLost', 'type' => Type::string()],
            'quantityRepair' => ['name' => 'quantityRepair', 'type' => Type::string()],
            'quantityBack' => ['name' => 'quantityBack', 'type' => Type::string()],
            'isCollected' => ['name' => 'isCollected', 'type' => Type::string()],
            'condition' => ['name' => 'condition', 'type' => Type::string()],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $byUser = Auth::user();

        $order = Order::find($args['order']);

        if(!$order) {

            throw new APIException('Order not found!');
        }

        if (!($byUser->hasAnyRole(array_merge(Role::MANAGER_ROLE_CODES)) || ($byUser->user_id == $order->user_id))) {

            throw new APIException('У вас нет прав для доступа к этой странице');
        }

        if (isset($args['contractor'])) {

            $contractor = Contractor::where('cmf_site_id', config('common.siteId', 1))
                ->where('contractor_id', $args['contractor'])->first();

            if(!$contractor) {

                throw new APIException('Contractor not found!');
            }
        }

        $item = Item::find(['item_id' => $args['item'], 'cmf_site_id' => config('common.siteId', 1)]);

        if(!$item) {

            throw new APIException('Item not found!');
        }

        if (!(isset($args['id']) && ($p = OrderComplect::find($args['id'])))) {

            $p = new OrderComplect();
        }

        $fields = ['order' => 'orders_id', 'item' => 'item_id', 'quantity' => 'quantity'
            , 'condition' => 'condition', 'isCollected' => 'is_collected', 'contractor' => 'contractor_id'
            , 'quantityLost' => 'quantity_lost', 'quantityRepair' => 'quantity_repair', 'quantityBack' => 'quantity_back'];

        foreach ($fields as $key => $value) {

            if (isset($args[$key])) {

                $p[$value] = $args[$key];
            }
        }

        $p->save();

        return $p;
    }
}