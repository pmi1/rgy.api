<?php

namespace App\GraphQL\Mutation;

use CLosure;
use App\User;
use App\Role;
use App\UserCashback;
use GraphQL;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use App\GraphQL\RguysMutation;
use Illuminate\Support\Facades\Auth;
use App\GraphQL\HasMiddleware;
use Carbon\Carbon;

class ChangeUserCashbackMutation extends RguysMutation
{
    protected $attributes = [
        'name' => 'ChangeUserCashback'
    ];

    public function type(): Type
    {
        return GraphQL::type('UserCashback');
    }

    public function args(): array
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::string()],
            'user' => ['name' => 'user', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
            'price' => ['name' => 'price', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
            'operation' => ['name' => 'operation', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
            'type' => ['name' => 'type', 'type' => Type::string()],
            'comment' => ['name' => 'comment', 'type' => Type::string()],
            'order' => ['name' => 'order', 'type' => Type::string()],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $byUser = Auth::user();

        if (!$byUser->hasPid() && !$byUser->hasAnyRole(array_merge(Role::MANAGER_ROLE_CODES))) {

            throw new APIException('У вас нет прав для доступа к этой странице');
        }

        $user = User::find($args['user']);

        if(!$user) {

            throw new APIException('User not found!');
        }

        if (!(isset($args['id']) && ($p = UserCashback::find($args['id'])))) {

            $p = new UserCashback();
        }

        $fields = ['order_id' => 'order', 'comment' => 'comment', 'price' => 'price'
            , 'type' => 'operation', 'CashbackType' => 'type'];

        foreach ($fields as $key => $value) {

            if (isset($args[$value])) {

                $p[$key] = $args[$value];
            }
        }

        $p->user_id = $user->user_id;
        $p->by_user_id = $byUser->user_id;
        $p->cdate = Carbon::now();
        $p->save();

        return $p;
    }
}