<?php

namespace App\GraphQL\Mutation;

use CLosure;
use App\User;
use App\Role;
use App\OrderLogistExec;
use App\Exceptions\APIException;
use App\CmfSetting;
use Helper;
use GraphQL;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Auth;
use App\GraphQL\HasMiddleware;
use Carbon\Carbon;

class OrderLogistExecMutation extends Mutation
{
    protected $attributes = [
        'name' => 'OrderLogistExec'
    ];

    public function type(): Type
    {
        return GraphQL::type('User');
    }

    public function args(): array
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::string()],
            'orders_logist_id' => ['name' => 'orders_logist_id', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
            'quantity' => ['name' => 'quantity', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
            'item_id' => ['name' => 'item_id', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
            'parent_id' => ['name' => 'parent_id', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
            'role_id' => ['name' => 'role_id', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
            'condition' => ['name' => 'condition', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
            'damaged' => ['name' => 'damaged', 'type' => Type::string()]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $byUser = Auth::user();

        if (!(isset($args['id']) && ($p = OrderLogistExec::find($args['id'])))) {

            $p = new OrderLogistExec();
        }

        $p->cdate = Carbon::now();
        $p->user_id = $byUser->user_id;

        $fields = ['orders_logist_id' => 'orders_logist_id', 'quantity' => 'quantity', 'item_id' => 'item_id'
            , 'condition' => 'condition', 'damaged' => 'damaged', 'reason' => 'reason'
            , 'role_id' => 'role_id', 'parent_id' => 'parent_id'];

        foreach ($fields as $key => $value) {

            if (isset($args[$key])) {

                $p[$value] = $args[$key];
            }
        }

        $p->save();

        return $p;


/*        $mail = Helper::mail(58, array(
            'to' => $user->email,
            'to_name' => $user->lastname.' '.$user->name,
            'mask' => [
                'CLIENT_NAME' => $user->name.' '.$user->secondname,
                'CLIENT_EMAIL' => $user->email,
                'CLIENT_EMAIL_CRYPT' => Helper::dsCrypt($user->email),
                'PROMOCODE' => $user->promocode,
            ],
        ));

        $smsText = str_replace(['[PROMOCODE]', '[CLIENT_NAME]'], [$user->promocode, $user->name.' '.$user->secondname], CmfSetting::getString('SMS_PROMOCODE'));
        $smsPhone = preg_replace('/[^0-9]+/', '', $user->phone);
        $smsUrl = str_replace(['[PHONE]', '[TEXT]'], [$smsPhone, urlencode($smsText)], CmfSetting::getString('SMS_GATE'));
        $response = file_get_contents($smsUrl);
        Helper::addSmsLog($user->user_id, 'Уведомление пользователю '.$user->email.' о присвоении промокода.', $smsUrl, $response);
*/
        return $user;
    }
}