<?php

namespace App\Observers;

use App\User;
use App\CmfSetting;
use Helper;

class UserObserver
{
    /**
     * Handle the user "created" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function created(User $user)
    {
        //
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        if($user->isDirty('user_category_id') && $user->userCategory) {

            $mail = Helper::mail(53, array(
                'to' => $user->email,
                'to_name' => $user->name.' '.$user->secondname,
                'mask' => [
                    'CLIENT_NAME' => $user->name.' '.$user->secondname,
                    'CLIENT_EMAIL' => Helper::dsCrypt($user->email),
                    'CLIENT_CATEGORY' => $user->userCategory->name,
                ],
            ));

            $smsText = str_replace(['[USER_CATEGORY]', '[CLIENT_NAME]', '[CLIENT_EMAIL]'], [$user->userCategory->name, $user->name.' '.$user->secondname, Helper::dsCrypt($user->email)], CmfSetting::getString('SMS_USER_CATEGORY'));
            $smsPhone = preg_replace('/[^0-9]+/', '', $user->phone);
            $smsUrl = str_replace(['[PHONE]', '[TEXT]'], [$smsPhone, urlencode($smsText)], CmfSetting::getString('SMS_GATE'));
            $response = file_get_contents($smsUrl);
            Helper::addSmsLog($user->user_id, 'Перевод пользователя №'.$user->user_id.'-'.$user->email.' в новую категорию '.$user->userCategory->name, $smsUrl, $response);
        }
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Handle the user "restored" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
