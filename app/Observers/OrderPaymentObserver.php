<?php

namespace App\Observers;

use App\OrderPayment;
use App\Order;
use App\User;
use App\Role;
use App\CmfSetting;
use Helper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OrderPaymentObserver
{
    /**
     * Handle the order "created" event.
     *
     * @param  \App\OrderPayment $orderPayment
     * @return void
     */
    public function created(OrderPayment $orderPayment)
    {
        //
    }

    /**
     * Handle the order "updated" event.
     *
     * @param  \App\OrderPayment $orderPayment
     * @return void
     */
    public function updated(OrderPayment $orderPayment)
    {

        $user = Auth::user();

        if($orderPayment->isDirty('status') && $orderPayment->status == 2) {

            $order = $orderPayment->order;

            $mail = Helper::mail(43, array(
                'to' => $order->email,
                'to_name' => $order->user->name.' '.$order->user->secondname,
                'mask' => [
                    'CLIENT_NAME' => $order->user->name.' '.$order->user->secondname,
                    'CLIENT_EMAIL' => Helper::dsCrypt($order->email),
                    'ORDER_NUMBER' => $order->orders_id,
                    'AMOUNT' => $orderPayment->amount,
                ],
            ));

            $smsText = str_replace(['[ORDERS_ID]', '[EMAIL]', '[AMOUNT]', '[CLIENT_NAME]'], [$order->orders_id, Helper::dsCrypt($order->email), $orderPayment->amount, $order->user->name.' '.$order->user->secondname], CmfSetting::getString('SMS_FOR_ORDER_PAYED_BY_USER'));
            $smsPhone = preg_replace('/[^0-9]+/', '', $order->phone);
            $smsUrl = str_replace(['[PHONE]', '[TEXT]'], [$smsPhone, urlencode($smsText)], CmfSetting::getString('SMS_GATE'));
            $response = file_get_contents($smsUrl);
            Helper::addSmsLog($user->user_id, 'Оплата заказа №'.$order->orders_id.' с номером №'.$orderPayment->id, $smsUrl, $response);

        }
    }

    /**
     * Handle the order "deleted" event.
     *
     * @param  \App\OrderPayment $orderPayment
     * @return void
     */
    public function deleted(OrderPayment $orderPayment)
    {
        //
    }

    /**
     * Handle the order "restored" event.
     *
     * @param  \App\OrderPayment $orderPayment
     * @return void
     */
    public function restored(OrderPayment $orderPayment)
    {
        //
    }

    /**
     * Handle the order "force deleted" event.
     *
     * @param  \App\OrderPayment $orderPayment
     * @return void
     */
    public function forceDeleted(OrderPayment $orderPayment)
    {
        //
    }
}
