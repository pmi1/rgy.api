<?php

namespace App;

use App\Order;
use App\Tax;
use App\User;
use App\LegalEntity;
use App\PaymentType;
use App\PaymentVariant;
use App\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Request;

class OrderPayment extends Model
{

    public $table = 'orders_payment';

    public $primaryKey = 'id';

    protected $fillable = ['legal_entity_id', 'amount', 'tax_id', 'status', 'orders_id', 'payment_type', 'payment_variant', 'payment_date', 'user_id', 'payment_at'];

    public function legalEntity()
    {
        return $this->belongsTo(LegalEntity::class, 'legal_entity_id', 'id');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id', 'id');
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class, 'payment_type', 'id');
    }

    public function paymentVariant()
    {
        return $this->belongsTo(PaymentVariant::class, 'payment_variant', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'orders_id', 'orders_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function paymentStatus()
    {
        return $this->belongsTo(PaymentStatus::class, 'status', 'id');
    }

    public function getUrlAttribute($returnUrl = '', $failUrl = '')
    {
        $result = '';

        if ($this->id) {

            $p = OrderPayment::find($this->id);
            $is = ['token' => $p->legalEntity ? $p->legalEntity->token : ''
                /*'userName' => 'rguys_1-api'
                , 'password' => 'rguys_1'*/
                , 'orderNumber' => $p->orders_id.'_'.$p->id.'_'.substr(md5(time()), 0, 12)
                , 'amount' => 0
                , 'taxSystem' => $p->tax ? $p->tax->sber : ''
                , 'returnUrl' => $returnUrl ? $returnUrl : 'https://'.env('MAIN_DOMAIN').'/dashboard/orders/order/'.$p->orders_id.'/?response=success&id='.$this->id
                , 'failUrl' => $failUrl ? $failUrl : 'https://'.env('MAIN_DOMAIN').'/dashboard/orders/order/'.$p->orders_id.'/?response=fail&id='.$this->id];

            if ($p->tax) {

                $cart = [];
                $t = (intval($p->order->rentpersonal) + intval($p->order->delivery) - ($p->order->cashback ? $p->order->cashback->price : 0)) / count($p->order->products);

                foreach ($p->order->products as $item) {

                    if ($item->product->landlord == $p->tax->id) {

                        $tp = $item->CalcPrice;
                        $price = $tp->discountedPrice * $tp->coefficient * 100;
                        $cart[] = ['positionId' => count($cart)+1,
                            'name' => $item->name,
                            'quantity' => [
                                'value' => $item->quantity,
                                'measure' => 'штук'
                            ],
                            'itemCode' => $item->article,
                            'itemPrice' => (string)$price,
                        ];
                        $is['amount'] += $price * $item->quantity;
                    }
                }

                $price = ceil($t*count($cart)) * 100;
                $cart[] = ['positionId' => count($cart)+1,
                    'name' => 'Техническое обслуживание',
                    'quantity' => [
                        'value' => 1,
                        'measure' => 'штук'
                    ],
                    'itemCode' => 'tech',
                    'itemPrice' => (string)$price
                ];
                $is['orderBundle'] = json_encode(['cartItems' => ['items' => $cart]], JSON_UNESCAPED_UNICODE);
                $is['amount'] += $price;

                $this->amount = $is['amount'] / 100;
                $this->save();
            }

            $ch = curl_init('https://securepayments.sberbank.ru/payment/rest/register.do');
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($is));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $t = curl_exec($ch);
            curl_close($ch);
            $this->sber = $t;
            $this->save();
            $t = json_decode($t);

            if (isset($t->formUrl)) {

                $result .= $t->formUrl;
            }
        }

        return $result;
    }
}