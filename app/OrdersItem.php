<?php

namespace App;

use App\Item;
use App\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrdersItem extends Model
{
    public $primaryKey = 'orders_item_id';

    public $table = 'orders_item';

    protected $fillable = ['item_id', 'orders_id', 'price', 'quantity', 'discount_percent_from_manager', 'discount_2_day', 'coefficient'];

    public function product()
    {
        return $this->belongsTo(Item::class, 'item_id',  'item_id')->where('item.cmf_site_id', config('common.siteId', 1));
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'orders_id',  'orders_id');
    }

    public function getCalcPriceAttribute()
    {
        return DB::table('orders_item as oi')
            ->select([
                DB::raw('ifnull(oi.discount_percent_from_manager,0) as discount_percent_from_manager'),
                DB::raw('if(oi.use_discount, ifnull(o.user_discount,0)+ifnull(oi.additional_discount,0), 0)+ifnull(oi.discount_percent_from_manager,0) as discount'),
                DB::raw('if(oi.coefficient>0, oi.coefficient, if(i.arendatype = 2, if(o.hour_rent_per_day, o.hour_rent_per_day/i._min_hours, 1)*if(o.rent_day, o.rent_day, CEIL(o.renthours/24)), (1+if(o.renthours > 24, (CEIL(o.renthours/24)-1)*oi.discount_2_day/100, 0)))) as coefficient'),
                DB::raw('CEIL(oi.full_price*(1-(if(oi.use_discount, ifnull(o.user_discount,0)+ifnull(oi.additional_discount,0), 0)+ifnull(oi.discount_percent_from_manager,0))/100)) as discountedPrice'),
                DB::raw('if(i.arendatype = 2, 
                (oi.price + if(o.hour_rent_per_day>i._min_hours, oi.price/i._min_hours*(o.hour_rent_per_day-i._min_hours), 0))*CEIL(o.renthours/24), 
   if(o.renthours <= 24 , oi.price, ROUND(oi.price*(1+(CEIL(o.renthours/24)-1)*oi.discount_2_day/100)))) as day_price_wd'), 'o.renthours'])
            ->leftJoin('orders as o', 'o.orders_id', '=', 'oi.orders_id')
            ->leftJoin(DB::raw('(select *, 
                    if(min_hours,min_hours, discount_2_day) _min_hours
                    from item) i'),function($join) {
                 $join->on('i.item_id', '=', 'oi.item_id');
                 $join->on('i.cmf_site_id', DB::raw($this->order->cmf_site_id));
             })
            ->where('oi.orders_item_id', $this->orders_item_id)
            ->first();
    }

    public function getBusyAttribute()
    {
        $from = date('Y-m-d', strtotime($this->order->installdate.' '.$this->order->installtime));
        $to = date('Y-m-d', strtotime($this->order->enddate.' '.$this->order->endtime));
        $fromtime = strtotime($from);
        $totime = strtotime($to);
        $result = [];

        $orders = DB::table('orders as o')
            ->select([
                    'o.orders_id', 'o.orders_status_id', 'o.installdate', 'o.enddate', 'oic.quantity'
                    ])
            ->leftJoin('orders_item as oi',function($join) {
                 $join->on('oi.orders_id', '=', 'o.orders_id');
             })
            ->leftJoin('orders_item_contractor as oic',function($join) {
                 $join->on('oic.orders_item_id', '=', 'oi.orders_item_id');
                 $join->on('oic.contractor_id', DB::raw(3));
             })
            ->whereNotNull('oic.contractor_id')
            ->whereNotNull('o.installdate')
            ->whereIn('o.orders_status_id', Order::busyOrderStatus)
            ->where('oi.item_id', $this->item_id)
            ->where('oi.orders_id', '!=', $this->orders_id)
            ->whereRaw('(STR_TO_DATE(o.installdate, "%Y-%c-%d") <= "'.$to.'") AND (STR_TO_DATE(o.enddate, "%Y-%c-%d") >= "'.$from.'")')
            ->get();

        foreach ($orders as $v) {

            $v = (array) $v;
            $loopdate = strtotime( $v['installdate'] );
            $enddate = strtotime( $v['enddate'] );

            while( $loopdate <= $enddate ) {

                if ($loopdate >= $fromtime && $loopdate <= $totime) {
                    $t = $this->item_id.'_'.$loopdate;

                    if (isset($result[$t])) {
                        $result[$t]['orders'][] = ['id' => $v['orders_id'], 'status' => $v['orders_status_id']];
                        $result[$t]['amount'] += $v['quantity'];
                    } else {
                       $result[$t] = array(
                            'date' => date('Y-m-d', $loopdate),
                            'amount' => $v['quantity'],
                            'orders' => [['id' => $v['orders_id'], 'status' => $v['orders_status_id']]],
                       );
                   }
               }

               $loopdate = strtotime( '+1 day', $loopdate );
            }
        }

        return $result;
    }


}