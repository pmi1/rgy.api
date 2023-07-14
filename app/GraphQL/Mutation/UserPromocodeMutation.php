<?php

namespace App\GraphQL\Mutation;

use CLosure;
use App\User;
use App\Role;
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

class UserPromocodeMutation extends Mutation
{
    protected $attributes = [
        'name' => 'UserPromocode'
    ];

    public function type(): Type
    {
        return GraphQL::type('User');
    }

    public function args(): array
    {
        return [
            'userId' => ['name' => 'userId', 'type' => Type::nonNull(Type::string()), 'rules' => ['required', 'string']],
            'generate' => ['name' => 'generate', 'type' => Type::string()],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $byUser = Auth::user();

        $user = User::find($args['userId']);

        if(!$user) {

            throw new APIException('User not found!');
        }

        if (!($byUser->hasAnyRole(array_merge(Role::MANAGER_ROLE_CODES)))) {

            throw new APIException('У вас нет прав для доступа к этой странице');
        }

        if (isset($args['generate'])) {

            $user->promocode = substr(md5(time()), 0, 8);
            $user->save();
        }

        $mail = Helper::mail(58, array(
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

        return $user;
    }
}