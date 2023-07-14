<?php

namespace App\GraphQL\Mutation;

use CLosure;
use App\User;
use App\Role;
use App\OrderLogist;
use App\Exceptions\APIException;
use App\OrderLogistHelper;
use GraphQL;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Auth;
use App\GraphQL\HasMiddleware;
use Carbon\Carbon;

class OrderLogistHelperMutation extends Mutation
{
    protected $attributes = [
        'name' => 'OrderLogistHelper'
    ];

    public function type(): Type
    {
        return Type::string();
    }

    public function args(): array
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::string()],
            'orderLogist' => ['name' => 'orderLogist', 'type' => Type::string()],
            'helper' => ['name' => 'helper', 'type' => Type::string()],
            'main' => ['name' => 'main', 'type' => Type::string()],
            'delete' => ['name' => 'delete', 'description' => 'Set 1 for remove record', 'type' => Type::string()]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $byUser = Auth::user();


        if (!($byUser->hasAnyRole(array_merge(Role::MANAGER_ROLE_CODES)))) {

            throw new APIException('У вас нет прав для доступа к этой странице');
        }

        if (!(isset($args['id']) && ($p = OrderLogistHelper::find($args['id'])))) {

            $p = new OrderLogistHelper();

        } elseif (isset($args['delete']) && ($args['delete'] == 1)) {

            return $p->delete();
        }

        if(!OrderLogist::find($args['orderLogist'])) {

            throw new APIException('OrderLogist not found!');
        }


        $fields = ['orderLogist' => 'orders_logist_id', 'helper' => 'name', 'main' => 'main'];

        foreach ($fields as $key => $value) {

            if (isset($args[$key])) {

                $p[$value] = $args[$key];
            }
        }

        return $p->save();
    }
}