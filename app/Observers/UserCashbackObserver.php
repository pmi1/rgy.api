<?php

namespace App\Observers;

use App\UserCashback;
use App\CmfSetting;
use Helper;

class UserCashbackObserver
{
    /**
     * Handle the user cashback "created" event.
     *
     * @param  \App\UserCashback  $userCashback
     * @return void
     */
    public function created(UserCashback $userCashback)
    {
        if (($userCashback->type == UserCashback::accrual) && ($user = $userCashback->user)) {

            $mail = Helper::mail($userCashback->comment == 'Бонус по реферальной программе' ? 57 : 52, array(
                'to' => $user->email,
                'to_name' => $user->lastname.' '.$user->name,
                'mask' => [
                    'CLIENT_NAME' => $user->name.' '.$user->secondname,
                    'CLIENT_EMAIL' => $user->email,
                    'CLIENT_EMAIL_CRYPT' => Helper::dsCrypt($user->email),
                    'ORDER_NUMBER' => $userCashback->order_id,
                    'CASHBACK' => $userCashback->price,
                ],
            ));

            $smsText = str_replace(['[ORDERS_ID]', '[CASHBACK]', '[CLIENT_NAME]'], [$userCashback->order_id, $userCashback->price, $user->name.' '.$user->secondname], CmfSetting::getString($userCashback->comment == 'Бонус по реферальной программе' ? 'SMS_BONUS' : 'SMS_CASHBACK'));
            $smsPhone = preg_replace('/[^0-9]+/', '', $user->phone);
            $smsUrl = str_replace(['[PHONE]', '[TEXT]'], [$smsPhone, urlencode($smsText)], CmfSetting::getString('SMS_GATE'));
            $response = file_get_contents($smsUrl);
            Helper::addSmsLog($user->user_id, 'Уведомление пользователю '.$user->email.' заказа №'.$userCashback->order_id.' о начислении кэшбэка.', $smsUrl, $response);
        }
    }

    /**
     * Handle the user cashback "updated" event.
     *
     * @param  \App\UserCashback  $userCashback
     * @return void
     */
    public function updated(UserCashback $userCashback)
    {
        //
    }

    /**
     * Handle the user cashback "deleted" event.
     *
     * @param  \App\UserCashback  $userCashback
     * @return void
     */
    public function deleted(UserCashback $userCashback)
    {
        //
    }

    /**
     * Handle the user cashback "restored" event.
     *
     * @param  \App\UserCashback  $userCashback
     * @return void
     */
    public function restored(UserCashback $userCashback)
    {
        //
    }

    /**
     * Handle the user cashback "force deleted" event.
     *
     * @param  \App\UserCashback  $userCashback
     * @return void
     */
    public function forceDeleted(UserCashback $userCashback)
    {
        //
    }
}
