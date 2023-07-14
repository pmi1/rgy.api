<?php

namespace App\GraphQL\Mutation;

use CLosure;
use App\User;
use App\Role;
use App\Order;
use App\Tax;
use App\LegalEntity;
use App\Exceptions\APIException;
use App\OrderPayment;
use GraphQL;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use App\GraphQL\RguysMutation;
use Illuminate\Support\Facades\Auth;
use App\GraphQL\HasMiddleware;
use Carbon\Carbon;

class OrderPaymentMutation extends RguysMutation
{
    protected $attributes = [
        'name' => 'OrderPayment'
    ];

    public function type(): Type
    {
        return GraphQL::type('OrderPayment');
    }

    public function args(): array
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::string()],
            'order' => ['name' => 'order', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
            'amount' => ['name' => 'amount', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
            'tax' => ['name' => 'tax', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
            'status' => ['name' => 'status', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
            'paymentType' => ['name' => 'paymentType', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
            'legalEntity' => ['name' => 'legalEntity', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
            'paymentDate' => ['name' => 'paymentDate', 'type' => Type::string()],
            'paymentVariant' => ['name' => 'paymentVariant', 'type' => Type::string()]
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

        $tax = Tax::find($args['tax']);

        if(!$tax) {

            throw new APIException('Tax not found!');
        }

        $legalEntity = LegalEntity::find($args['legalEntity']);

        if(!$legalEntity) {

            throw new APIException('LegalEntity not found!');
        }

        if (!(isset($args['id']) && ($p = OrderPayment::find($args['id'])))) {

            $p = new OrderPayment();

        } elseif (isset($args['status']) && $args['status'] == 2) {

            $p->payment_at = Carbon::now();
            $p->user_id = $byUser->user_id;
        }

        $fields = ['order' => 'orders_id', 'status' => 'status', 'amount' => 'amount'
            , 'tax' => 'tax_id', 'legalEntity' => 'legal_entity_id', 'paymentType' => 'payment_type'
            , 'paymentDate' => 'payment_date', 'paymentVariant' => 'payment_variant'];

        foreach ($fields as $key => $value) {

            if (isset($args[$key])) {

                $p[$value] = $args[$key];
            }
        }

        $p->save();

        return $p;
    }
}