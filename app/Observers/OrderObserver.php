<?php

namespace App\Observers;

use App\Order;
use App\User;
use App\Role;
use App\OrderPayment;
use App\UserCategory;
use App\UserCashback;
use App\OrderStatusLog;
use App\CmfSetting;
use Helper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OrderObserver
{
    /**
     * Handle the order "created" event.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        //
    }

    /**
     * Handle the order "updated" event.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function updated(Order $order)
    {

        $user = Auth::user();

        if ($order->isDirty('payment_type') 
            && ($order->payment_type == 'Картой' || $order->payment_type == 2)) {

            foreach ($order->amountForTax as $tax) {

                if (! OrderPayment::where('orders_id', $order->orders_id)
                        ->where('payment_type', 2)
                        ->where('tax_id', $tax['tax']->id)
                        ->whereIn('status', [1,2])->first()) {

                    $t = new OrderPayment;
                    $t->orders_id = $order->orders_id;
                    $t->legal_entity_id = 2; 
                    $t->tax_id = $tax['tax']->id;
                    $t->user_id = $user->user_id;
                    $t->status = 1;
                    $t->amount = $tax['amount'];
                    $t->payment_type = 2;
                    $t->payment_variant = 2;
                    $t->save();
                }
            }
        } elseif($order->isDirty('payment_type')) {

            $is = OrderPayment::where('orders_id', $order->orders_id)
                ->where('payment_type', 2)->where('status', 1)->get();

            foreach ($is as $t) {

                $t->status = 3;
                $t->save();
            }
        }

        if ($order->isDirty('feedbackAnswer') && $order->feedbackAnswer) {

            $t = new OrderStatusLog;
            $t->orders_id = $order->orders_id;
            $t->status_id = 1001;
            $t->comment = 'Ответ: '.$order->feedbackAnswer;
            $t->user_id = $user->user_id;
            $t->cdate = Carbon::now();
            $t->save();
        }

        if (($order->isDirty('feedback') && $order->feedback) || ($order->isDirty('ocenka') && $order->ocenka)) {

            $t = new OrderStatusLog;
            $t->orders_id = $order->orders_id;
            $t->status_id = 1000;
            $t->comment = 'Оценка: '.$order->ocenka.' Отзыв: '.$order->feedback;
            $t->user_id = $user->user_id;
            $t->cdate = Carbon::now();
            $t->save();
        }

        if ($order->isDirty('orders_status_id') && $order->orders_status_id == 24 && $order->manager) {

            $mail = Helper::mail(21, array(
                'to' => $order->manager->email,
                'to_name' => $order->manager->lastname.' '.$order->manager->name,
                'mask' => [
                    'ORDER_ID' => $order->orders_id,
                ],
            ));
        }

        if ($order->isDirty('ocenka') && $order->ocenka >= 4 && !$order->user->promocode && !in_array($order->user->user_category_id, [16, 18])) {

            $user = $order->user;
            $user->promocode = substr(md5(time()), 0, 8);
            $user->save();

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
        }

        if ($order->isDirty('orders_status_id') && $order->orders_status_id == 9 && $order->promocode) {

            $p = new UserCashback;
            $p->order_id = $order->orders_id;
            $p->type = UserCashback::accrual;
            $p->price = ceil($order->rentprice*$order->cashback_promocode/100);
            $p->user_id = $order->promocodeUser->user_id;
            $p->by_user_id = $user->user_id;
            $p->comment = 'Бонус по реферальной программе';
            $p->cdate = Carbon::now();
            $p->save();
        }

        if($order->isDirty('orders_status_id') && $order->orders_status_id == 9 
            && !$order->user->user_category_fixed) {

            if (($userCategory = UserCategory::where('limit', '<', $order->user->doneOrderSum)->orderBy('limit', 'desc')->first())
                && ($userCategory->user_category_id != $order->user->user_category_id)) {

                $order->user->user_category_id = $userCategory->user_category_id;
                $order->user->save();
            }
        }

        if($order->isDirty('orders_status_id') && $order->orders_status_id == 3) {

            $mail = Helper::mail(23, array(
                'to' => $order->email,
                'to_name' => $order->user->name.' '.$order->user->secondname,
                'mask' => [
                    'CLIENT_NAME' => $order->user->name.' '.$order->user->secondname,
                    'CLIENT_EMAIL' => Helper::dsCrypt($order->email),
                    'ORDER_NUMBER' => $order->orders_id,
                ],
            ));

            $smsText = str_replace(['[ORDERS_ID]', '[EMAIL]', '[CLIENT_NAME]'], [$order->orders_id, Helper::dsCrypt($order->email), $order->user->name.' '.$order->user->secondname], CmfSetting::getString('SMS_FOR_ORDER_ESTIMATE_BY_USER'));
            $smsPhone = preg_replace('/[^0-9]+/', '', $order->phone);
            $smsUrl = str_replace(['[PHONE]', '[TEXT]'], [$smsPhone, urlencode($smsText)], CmfSetting::getString('SMS_GATE'));
            $response = file_get_contents($smsUrl);
            Helper::addSmsLog($user->user_id, 'Сохранение менеджером заказа №'.$order->orders_id.' со статусом выполнен.', $smsUrl, $response);

        }

        if($order->isDirty('orders_status_id') && $order->orders_status_id == 20) {

            $mail = Helper::mail(56, array(
                'to' => $order->email,
                'to_name' => $order->user->name.' '.$order->user->secondname,
                'mask' => [
                    'CLIENT_NAME' => $order->user->name.' '.$order->user->secondname,
                    'CLIENT_EMAIL' => Helper::dsCrypt($order->email),
                    'ORDER_NUMBER' => $order->orders_id,
                ],
            ));

            $smsText = str_replace(['[ORDERS_ID]', '[EMAIL]', '[CLIENT_NAME]'], [$order->orders_id, Helper::dsCrypt($order->email), $order->user->name.' '.$order->user->secondname], CmfSetting::getString('SMS_FOR_ORDER_CAN_PAY_BY_USER'));
            $smsPhone = preg_replace('/[^0-9]+/', '', $order->phone);
            $smsUrl = str_replace(['[PHONE]', '[TEXT]'], [$smsPhone, urlencode($smsText)], CmfSetting::getString('SMS_GATE'));
            $response = file_get_contents($smsUrl);
            Helper::addSmsLog($user->user_id, 'Сохранение менеджером заказа №'.$order->orders_id.' со статусом ждем оплаты.', $smsUrl, $response);
        }

        if($order->isDirty('orders_status_id') && $order->orders_status_id == 38) {

            $managerObject = $order->manager;
            $orderM = Order::getOrderById($order->orders_id);

            if (isset($orderM['item'])) {

                 $itemsHtml = \View::make('mail.order', ['orderItem' => $orderM['item']])->render();

            }

            $cs = User::whereHas('roles', function ($query){
                    $query->whereIn('code', Role::CONTRACTOR_ROLE_CODES);
                })
                ->where('exchangeNotifications', 1)
                ->get([
                    'email',
                    'name',
                    'lastname'
                ]);

            foreach ($cs as $contractorObject) {

                $mail = Helper::mail(51, array(
                    'to' => $contractorObject->email,
                    'to_name' => $contractorObject->name.' '.$contractorObject->secondname,
                    'mask' => [
                        'CLIENT_NAME' => $contractorObject->name.' '.$contractorObject->secondname,
                        'CLIENT_EMAIL' => $contractorObject->email,
                        'CLIENT_EMAIL_CRYPT' => Helper::dsCrypt($contractorObject->email),
                        'ORDER_NUMBER' => $order->orders_id,
                        'ORDER_DATE' => $order->date_insert,
                        'CLIENT_PHONE' => $order->phone,
                        'CLIENT_ADDRESS' => $order->address,
                        'CLIENT_INFO' => $order->description,
                        'MANAGER_NAME' => $managerObject->name.' '.$managerObject->lastname,
                        'MANAGER_PHONE' => $managerObject->phone,
                        'CASHBACK' => $orderM['availableCashback'],
                        'USER_TOVAR' => array($itemsHtml, 'html'),
                        'USER_INSTALLDATE' => $order->installdate,
                        'USER_INSTALLTIME' => $order->installtime,
                        'USER_ENDDATE' => $order->enddate,
                        'USER_ENDTIME' => $order->endtime,
                        'USER_RENTHOURS' => $order->renthours,
                        'USER_RENTPRICE' => number_format($order->rentprice, 0, '', ' '),
                        'USER_ONLYRENTPRICE' => number_format($order->rentprice, 0, ',', ' '),
                        'USER_DELIVERY' => $order->delivery,
                        'USER_CLIMB_VALUE' => $order->climb,
                        'USER_ASSEMBLY_VALUE' => $order->assembly,
                        'USER_RENTPERSONAL' => $order->rentpersonal,
                        'USER_SERVICE_PRICE' => number_format(floatval($order->rentpersonal) + floatval($order->delivery), 0, ',', ' '),
                        'USER_RENTOVERWORK' => $order->rentoverwork,
                        'USER_AMOUNT' => number_format($order->amount, 0, '', ' '),
                        'PLEDGE' => number_format($orderM['pledge'] , 0, '', ' '),
                    ],
                ));
            }
        }
    }

    /**
     * Handle the order "deleted" event.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function deleted(Order $order)
    {
        //
    }

    /**
     * Handle the order "restored" event.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the order "force deleted" event.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        //
    }
}
