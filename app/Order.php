<?php

namespace App;

use App\SearchFilters\ItemSearch\ItemSearchResult;
use App\SearchFilters\OrderSearch\OrderSearch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Item;
use App\User;
use App\CmfSite;
use App\UserCashback;
use App\OrderStatusLog;
use App\OrdersItem;
use App\OrderItemContractor;
use App\OrderStatus;
use App\OrderPayment;
use App\OrdersCancelReason;
use App\OrderGoal;
use App\Orderlogist;
use App\OrderDocument;
use App\OrderEventType;
use App\OrderRentRefundReason;
use App\OrderServiceRefundReason;
use App\OrderStatusComment;
use App\OrdersPaymentStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public $table = 'orders';

    const doneStatus = [3, 9, 26];
    const contractorStatus = [38, 39];
    const contractorBuyStatus = [38];
    const busyOrderStatus = [6,10,11,12,7,8,16,17,18,21,35,20,24,26];
    const busyOrderStatusStock = [2, 14, 10, 11, 12, 35, 6, 16, 24, 20, 26];

    protected $fillable = ['operator_id', 'orders_status_id', 'orders_payment_status_id', 'car_amount_our', 'car_amount', 'car_amount_their', 'worker_amount_our', 'worker_amount', 'worker_amount_their', 'delivery', 'delivery_internal_our', 'delivery_internal_their', 'rentpersonal', 'rentpersonal_internal_our', 'rentpersonal_internal_their', 'procatilo', 'foreign_delivery', 'is_pickup', 'order_cancel_reason_id', 'description_logist', 'description_warehouse', 'description_driver', 'description_manager', 'description', 'address', 'email', 'phone', 'installdate', 'installtime', 'enddate', 'endtime', 'contact_name', 'contact_phone', 'payment_type', 'payment_date', 'user_id', 'deposit', 'reserv_time', 'stage', 'event_start', 'event_finish', 'call_date', 'renthours', 'rentprice', 'amount', 'rent_day', 'hour_rent_per_day', 'deposit_received', 'deposit_returned', 'date_insert', 'rentzalog', 'prepayment', 'finalized', 'taxiConsumption', 'mkadkm', 'liftDimentions', 'parking', 'distance', 'parkingPrice', 'entryHeight', 'elevator', 'elevatorDistance', 'corridorWidth', 'riseToFloor', 'stepsWidth', 'stepsTurnWidth', 'stageScheme', 'pass', 'location', 'fio', 'cmf_site_id', 'feedback', 'ocenka', 'alternative', 'settlement_date', 'orders_goal_id', 'orders_event_type_id', 'feedbackAnswer', 'sber', 'promocode'];


    public $primaryKey = 'orders_id';

    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'orders_status_id', 'orders_status_id');
    }

    public function paymentStatus()
    {
        return $this->belongsTo(OrdersPaymentStatus::class, 'orders_payment_status_id', 'id');
    }

    public function promocodeUser()
    {
        return $this->hasOne(User::class, 'promocode', 'promocode');
    }

    public function userCategory()
    {
        return $this->belongsTo(UserCategory::class, 'user_category_id',  'user_category_id');
    }

    public function cmfSite()
    {
        return $this->belongsTo(CmfSite::class, 'cmf_site_id', 'cmf_site_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function log()
    {
        return $this->hasMany(OrderStatusLog::class, 'orders_id', 'orders_id');
    }

    public function logist()
    {
        return $this->hasMany(Orderlogist::class, 'orders_id', 'orders_id');
    }

    public function documents()
    {
        return $this->hasMany(OrderDocument::class, 'order_id', 'orders_id');
    }

    public function payments()
    {
        return $this->hasMany(OrderPayment::class, 'orders_id', 'orders_id');
    }

    public function products()
    {
        return $this->hasMany(OrdersItem::class, 'orders_id', 'orders_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function decline()
    {
        return $this->belongsTo(OrdersCancelReason::class, 'order_cancel_reason_id', 'id');
    }

    public function cashback()
    {
        return $this->belongsTo(UserCashback::class, 'orders_id', 'order_id');
    }

    public function statusComment()
    {
        return $this->hasMany(OrderStatusComment::class, 'orders_id', 'orders_id');
    }

    public function eventType()
    {
        return $this->belongsTo(OrderEventType::class, 'orders_event_type_id', 'id');
    }

    public function customerNeeds()
    {
        return $this->belongsTo(OrderGoal::class, 'orders_goal_id', 'id');
    }

    public function rentRefundReason()
    {
        return $this->belongsTo(OrderRentRefundReason::class, 'rent_refund_reason_id', 'id');
    }

    public function serviceRefundReason()
    {
        return $this->belongsTo(OrderServiceRefundReason::class, 'service_refund_reason_id', 'id');
    }


    public function getOuterRentAttribute()
    {
        $result = 0;

        if ($this->orders_id) {

            $result = DB::table('orders')
                ->leftJoin('orders_item as oi',function($join) {
                     $join->on('oi.orders_id', '=', 'orders.orders_id');
                 })
                ->leftJoin('orders_item_contractor as oic',function($join) {
                     $join->on('oic.orders_item_id', '=', 'oi.orders_item_id');
                 })
                ->where('orders.orders_id', $this->orders_id)
                ->sum(DB::raw('if(oic.contractor_id <> 3 and oic.status = 1, oic.price * oic.quantity, 0)'));
        }

        return $result;
    }

    public function getAmountForTaxAttribute()
    {

        $result = [];
        $t = (intval($this->rentpersonal) + intval($this->delivery) - ($this->cashback ? $this->cashback->price : 0)) / count($this->products);

        foreach ($this->products as $item) {

            $tp = $item->CalcPrice;
            $price = $tp->discountedPrice * $tp->coefficient;
            if ($product = Item::find(['item_id' => $item->item_id, 'cmf_site_id' => $this->cmf_site_id])) {

                if (array_key_exists($product->tax->id, $result)) {

                    $result[$product->tax->id]['amount'] += $item->quantity * $price;
                    $result[$product->tax->id]['count']++;
                } else {

                    $result[$product->tax->id] = ['tax' => $product->tax, 'amount' => $item->quantity * $price, 'count' => 1];
                }
            }
        }

        foreach ($result as &$i) {

            $i['amount'] = ceil($i['amount'] + $t*$i['count']);
        }

        unset($i);

        return $result;
    }

    public function getInnerRentAttribute()
    {
        $result = 0;

        if ($this->orders_id) {

            $result = intval(Order::find($this->orders_id)->rentprice) - $this->getOuterRentAttribute();
        }

        return $result;
    }


    public function getOuterServiceAttribute()
    {
        $result = 0;

        if ($this->orders_id) {

            $result = DB::table('orders')
                ->leftJoin('orders_item as oi',function($join) {
                     $join->on('oi.orders_id', '=', 'orders.orders_id');
                 })
                ->leftJoin('orders_item_contractor as oic',function($join) {
                     $join->on('oic.orders_item_id', '=', 'oi.orders_item_id');
                 })
                ->where('orders.orders_id', $this->orders_id)
                ->sum(DB::raw('if(oic.contractor_id <> 3 and oic.status = 1, ifnull(oic.price_delivery, 0) + ifnull(oic.price_montag, 0), 0)'));
        }

        return $result;
    }

    public function getServiceProfitAttribute()
    {
        $result = 0;

        if ($this->orders_id) {

            $result = DB::table('orders')
                ->where('orders.orders_id', $this->orders_id)
                ->sum(DB::raw('(orders.delivery + orders.rentpersonal - orders.rentpersonal_internal_our - orders.rentpersonal_internal_their - orders.delivery_internal_our - orders.delivery_internal_their - ifnull(orders.taxiConsumption,0))')) - $this->getOuterServiceAttribute();
        }

        return $result;
    }

    public function getStatusDescriptionAttribute()
    {
        $result = '';

        if ($this->orders_id) {

            $result = ($t = DB::table('orders as o')
                ->select(['osc.comment as statusDescription'])
                ->leftJoin('orders_status_comment as osc',function($join) {
                     $join->on('osc.orders_id', '=', 'o.orders_id');
                     $join->on('osc.orders_status_id', '=', 'o.orders_status_id');
                 })
                ->where('o.orders_id', $this->orders_id)->first()) ? $t->statusDescription : '';
        }

        return $result;
    }

    public function getInstallDoneDateAttribute()
    {
        $result = '';

        if ($this->orders_id) {

            $result = ($t = DB::table('orders as o')
                ->select(['ol1.finish as installDoneDate'])
                ->leftJoin('orders_logist as ol1',function($join) {
                     $join->on('ol1.orders_id', '=', 'o.orders_id');
                     $join->on('ol1.type', DB::raw(3));
                 })
                ->where('o.orders_id', $this->orders_id)->first()) ? $t->installDoneDate : '';
        }

        return $result;
    }


    public function getUninstallDoneDateAttribute()
    {
        $result = '';

        if ($this->orders_id) {

            $result = ($t = DB::table('orders as o')
                ->select(['ol1.finish as uninstallDoneDate'])
                ->leftJoin('orders_logist as ol1',function($join) {
                     $join->on('ol1.orders_id', '=', 'o.orders_id');
                     $join->on('ol1.type', DB::raw(4));
                 })
                ->where('o.orders_id', $this->orders_id)->first()) ? $t->uninstallDoneDate : '';
        }

        return $result;
    }

    public function getPayfromCashbackAttribute()
    {
        $result = '';

        if ($this->orders_id) {

            $result = ($t = DB::table('orders as o')
                ->select(['uc.price'])
                ->leftJoin('user_cashback as uc',function($join) {
                     $join->on('uc.order_id', '=', 'o.orders_id');
                     $join->on('uc.user_id', '=', 'o.user_id');
                     $join->whereIn('uc.type', [UserCashback::payment, UserCashback::paymentBlocked]);
                 })
                ->where('o.orders_id', $this->orders_id)->first()) ? $t->price : '';
        }

        return $result;
    }

    public function getContractorsAttribute()
    {

        $contractors = Contractor::join('item_contractor', 'contractor.contractor_id', 'item_contractor.contractor_id')
            ->join('orders_item', 'item_contractor.item_id', 'orders_item.item_id')
            ->where('orders_item.orders_id', $this->getkey())
            ->where('item_contractor.cmf_site_id', config('common.siteId', 1))
            ->where('contractor.cmf_site_id', config('common.siteId', 1))
            ->groupBy('contractor.contractor_id')
            ->select(['contractor.*'])
            ->get();

        return $contractors;
    }

    public function accessories()
    {
        return $this->hasManyThrough(ItemComplect::class, OrdersItem::class, 'orders_id', 'parent_id', 'orders_id', 'item_id')
            ->join('item', 'item_complect.item_id', 'item.item_id')
            ->groupBy('item.item_id');
    }

    public function getProposalsAttribute()
    {

        $is = OrderItemContractor::join('orders_item', 'orders_item_contractor.orders_item_id', 'orders_item.orders_item_id')
            ->select([
                'orders_item_contractor.*'])
            ->where('orders_item.orders_id', $this->getkey())
            ->get();

        $is2 = ItemContractor::join('orders_item', 'item_contractor.item_id', 'orders_item.item_id')
            ->select([
                'item_contractor.*'
                , 'orders_item.orders_item_id'])
            ->leftJoin('contractor', 'contractor.contractor_id', 'item_contractor.contractor_id')
            ->whereNotNull('contractor.contractor_id')
            ->where('orders_item.orders_id', $this->getkey())
            ->where('item_contractor.cmf_site_id', config('common.siteId', 1))
            ->where('contractor.cmf_site_id', config('common.siteId', 1))
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('orders_item_contractor')
                      ->join('orders_item', 'orders_item_contractor.orders_item_id', 'orders_item.orders_item_id')
                      ->where('orders_item.orders_id', $this->getkey())
                      ->whereRaw('orders_item_contractor.contractor_id = item_contractor.contractor_id')
                      ->whereRaw('orders_item.item_id = item_contractor.item_id');
            })
            ->get();

        foreach ($is2 as $value) {

            $t = new OrderItemContractor();
            $t->status = 0;
            $t->price = $value->price;
            $t->contractor_id = $value->contractor_id;
            $t->orders_item_id = $value->orders_item_id;
            $is->push($t);
        }

        return $is;
    }

    public function getItem($id, $mode = 1)
    {
        $result = DB::table('orders as o')
            ->select([
                    'o.orders_id as id',
                    'o.operator_id as managerId',
                    'o.orders_status_id as status',
                    'o.orders_payment_status_id as paymentStatus',
                    'o.amount as price',
                    'o.date_insert as ordered',
                    'o.prepayment as paymentSum',
                    'o.deposit_received as isPledgeReceived',
                    'o.deposit_returned as isPledgeReturned',
                    'o.reserv_time as bookingDate',
                    DB::raw('concat(o.installdate, " ", o.installtime) as installDate, concat(o.enddate, " ", o.endtime) as uninstallDate'),
                    'osc.comment as statusDescription',
                    's.ordered as customerOrdered',
                    's.done as customerDone',
                    'o.phone as customerPhone',
                    'o.email as customerEmail',
                    'o.user_id as customerId',
                    'o.stage_id',
                    'o.finalized',
                    'o.mkadkm as mkadDistance',
                    'o.taxiConsumption',
                    'o.contact_name as installResponsible',
                    'o.contact_phone as installResponsiblePhone',
                    'o.settlement_date as calculationDate',
                    'o.alternative as alternativesAgree',
                    'o.orders_goal_id',
                    'o.orders_event_type_id as eventType',
                    'customer.status as customerStatus',
                    'customer.discounter_id as customerCategory',
                    'customer.name as customerName',
                    'customer.lastname as customerLastName',
                    'customer.secondname as customerSecondName',
                    'customer.companyname as customerCompany',
                    'customer.comment as customerDescription',
                    'customer.discount as customerDiscount',
                    'customer.bonus_points as customerBonus',
                    'ol1.finish as installDoneDate',
                    'ol2.finish as uninstallDoneDate',
                    'o.car_amount_our as innerCars',
                    'o.car_amount as cars',
                    'o.car_amount_their as outerCars',
                    'o.worker_amount_our as innerPersonnel',
                    'o.worker_amount as personnel',
                    'o.worker_amount_their as outerPersonnel',
                    'o.delivery as deliveryPrice',
                    'o.delivery_internal_our as innerDeliveryPrice',
                    'o.delivery_internal_their as outerDeliveryPrice',
                    'o.rentpersonal as installPrice',
                    'o.rentpersonal_internal_our as innerInstallPrice',
                    'o.rentpersonal_internal_their as outerInstallPrice',
                    'o.procatilo as rollup',
                    'o.foreign_delivery as foreignDelivery',
                    'o.is_pickup as selfDelivery',
                    'o.order_cancel_reason_id as declineId',
                    'o.description_logist as noteLogistician',
                    'o.description_warehouse as noteStock',
                    'o.description_driver as noteDriver',
                    'o.description_manager as noteManager',
                    'o.description as noteCustomer',
                    'o.stage',
                    'o.payment_type as paymentType',
                    'o.payment_date as paymentDate',
                    'o.address',
                    'o.event_start as eventStart',
                    'o.event_finish as eventEnd',
                    'o.call_date as callDate',
                    'o.rent_day as daysChanged',
                    'o.hour_rent_per_day as hoursChanged',
                    'o.liftDimentions as elevatorSizes',
                    'o.parking',
                    'o.distance',
                    'o.parkingPrice',
                    'o.entryHeight',
                    'o.elevator',
                    'o.elevatorDistance',
                    'o.corridorWidth',
                    'o.riseToFloor',
                    'o.stepsWidth',
                    'o.stepsTurnWidth',
                    'o.stageScheme',
                    'o.pass',
                    'o.location',
                    'o.ocenka as rating',
                    'o.feedback',
                    'o.feedbackAnswer',
                    'uc.price as payFromCashback',
                    ])
            ->where('o.orders_id', $id)
            ->leftJoin('user_cashback as uc',function($join) {
                 $join->on('uc.order_id', '=', 'o.orders_id');
                 //$join->on('uc.user_id', '=', 'o.user_id');
                 $join->whereIn('uc.type', [UserCashback::payment, UserCashback::paymentBlocked]);
             })
            ->leftJoin('orders_status_comment as osc',function($join) {
                 $join->on('osc.orders_id', '=', 'o.orders_id');
                 $join->on('osc.orders_status_id', '=', 'o.orders_status_id');
             })
            ->leftJoin('orders_logist as ol1',function($join) {
                 $join->on('ol1.orders_id', '=', 'o.orders_id');
                 $join->on('ol1.type', DB::raw(3));
             })
            ->leftJoin('orders_logist as ol2',function($join) {
                 $join->on('ol2.orders_id', '=', 'o.orders_id');
                 $join->on('ol2.type', DB::raw(4));
             })
            ->leftJoin('user as customer', 'customer.user_id', '=', 'o.user_id')
            ->leftJoin(DB::raw('(select user_id
                    , count(DISTINCT orders.orders_id) as ordered
                    , sum(if(orders.orders_status_id = 3 or orders.orders_status_id = 9, 1, 0)) as done 
                from `orders` group by user_id) as s'), 's.user_id', '=', 'o.user_id')
            ->get();
        if ($result->count()) {

            $result = $this->itemConvert($result)[0];
            $result['accessories'] = $result['proposals'] = [];
            $ms = ['parking', 'distance', 'parkingPrice', 'entryHeight', 'elevator', 'elevatorDistance', 'corridorWidth', 'riseToFloor', 'stepsWidth', 'stepsTurnWidth', 'stageScheme', 'address', 'elevatorSizes', 'pass', 'location'];
            $result['customerNeeds'] = $result['orders_goal_id'];
            $result['stage'] = ['name' => $result['stage']];
            $itemObject = new Item();

            foreach ($ms as $v) {

                $result['stage'][$v] = $result[$v];
            }

            if ($mode == 1) {

                $result["statuses"] = DB::table('orders_status_log as osl')
                    ->select([
                            'osl.cdate as date',
                            DB::raw('concat(u.lastname, " ", u.name) as user'),
                            'osl.status_id as status',
                            ])
                    ->where('osl.orders_id', $id)
                    ->leftJoin('user as u', 'u.user_id', '=', 'osl.user_id')
                    ->orderBy('osl.cdate')
                    ->get();
                $result["paymentStatuses"] = DB::table('orders_payment_status_log as osl')
                    ->select([
                            'osl.cdate as date',
                            DB::raw('concat(u.lastname, " ", u.name) as user'),
                            'osl.status_id as status',
                            'ops.name',
                            ])
                    ->leftJoin('orders_payment_status as ops', 'ops.id', '=', 'osl.id')
                    ->leftJoin('user as u', 'u.user_id', '=', 'osl.user_id')
                    ->where('osl.orders_id', $id)
                    ->orderBy('osl.cdate')
                    ->get();

            }

            $proposals = DB::table('orders_item as oi')
                ->select([
                        'ic.contractor_id',
                        'i.item_id as forId',
                        'i.typename as name',
                        'i.itemtype as type',
                        'i.article as art',
                        'i.stelag',
                        'ii.image',
                        'ic.available_quantity',
                        'oic.status',
                        'oic.quantity as amount',
                        'oic.install',
                        'oic.uninstall',
                        'oic.deposit as pledge',
                        'oic.delivery as deliveryIn',
                        'oic.pickup as deliveryOut',
                        'oic.price_montag as installPrice',
                        'oic.price_delivery as deliveryPrice',
                        DB::raw('IFNULL(`oic`.`orders_item_contractor_id`, `ic`.`id`) as id, 
                            IFNULL(`oic`.`state`, `ic`.`state`) `condition`, 
                            IFNULL(`oic`.`price`, `ic`.`price`) price,
                            `oic`.`status` = 1 as checked')
                        ])
                ->leftJoin('item_contractor as ic',function($join) {
                     $join->on('oi.item_id', '=', 'ic.item_id');
                     $join->on('ic.cmf_site_id', DB::raw(config('common.siteId', 1)));
                 })
                ->leftJoin('item as i',function($join) {
                     $join->on('i.item_id', '=', 'oi.item_id');
                     $join->on('i.cmf_site_id', DB::raw(config('common.siteId', 1)));
                 })
                ->leftJoin('orders_item_contractor as oic',function($join) {
                     $join->on('oic.orders_item_id', '=', 'oi.orders_item_id');
                     $join->on('oic.contractor_id', '=', 'ic.contractor_id');
                 })
                ->leftJoin('item_image as ii',function($join) {
                     $join->on('ii.item_id', '=', 'i.item_id');
                     $join->on('ii.cmf_site_id', '=', 'i.cmf_site_id');
                     $join->on('ii.ordering', DB::raw(1));
                 })
                ->where('oi.orders_id', $id)
                ->get();

            $proposals = $itemObject->prepare($proposals);

            foreach ($proposals as $key => $value) {
                $value = (array)$value;
                $result['proposals'][$value['contractor_id']]['products'][] = $value;
            }

            $contractors = DB::table('contractor as c')
                    ->select([
                            'c.contractor_id as id',
                            'c.name as contractorName',
                            'c.discount_percent_for_rguys as discount',
                            'c.image as contractorImage'
                            ])
                    ->whereIn('c.contractor_id', array_keys($result['proposals']))
                    ->where('c.cmf_site_id', config('common.siteId', 1))
                    ->get();

            foreach ($contractors as &$value) {

                $f = explode('#', $value->contractorImage);
                $value->contractorImage = $f[0];
                $value->products = $result['proposals'][$value->id]['products'];
                $value->visible = $result['proposals'][$value->id]['products'][0]['status'] != 2;
                $result['proposals'][$value->id] = $value;
            }

            unset($value);
            $result['proposals'] = array_values($result['proposals']);

            $result['products'] = $itemObject->prepare(DB::table('orders_item as oi')
                ->select([
                        'oi.item_id as id',
                        DB::raw('ifnull(i.itemtype, oi.name) as name'),
                        'i.typename as type',
                        'i.cashback',
                        'i.payfrom_cashback as payfromCashback',
                        'oi.article as art',
                        'i.availablecount as available',
                        'i.stelag',
                        'ii.image',
                        'oi.quantity as amount',
                        'oi.price as price',
                        'oi.discount_percent_from_manager as customDiscount',
                        'i.repair',
                        'i.arendatype as rentType',
                        'i.min_hours as minimalHours',
                        'oi.discount_2_day as secondDayDiscount'
                        ])
                ->leftJoin('item as i',function($join) {
                     $join->on('i.item_id', '=', 'oi.item_id');
                     $join->on('i.cmf_site_id', DB::raw(1));
                 })
                ->leftJoin('item_image as ii',function($join) {
                     $join->on('ii.item_id', '=', 'i.item_id');
                     $join->on('ii.cmf_site_id', '=', 'i.cmf_site_id');
                     $join->on('ii.ordering', DB::raw(1));
                 })
                ->where('oi.orders_id', $id)
                ->get());

            $from = date('Y-m-d', strtotime($result['installDate']));
            $to = date('Y-m-d', strtotime($result['uninstallDate']));
            $fromtime = strtotime($from);
            $totime = strtotime($to);
            $result['availableCashback'] = 0;

            foreach ($result['products'] as $key=>$value) {
                $value = (array) $value;

                if ($value['cashback']) {
                    $result['availableCashback'] += round((1 - $value['customDiscount']/100) *$value['price'] * $value['amount'] * $value['cashback'] / 100);
                }

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
                    ->whereNotNull('o.installdate')
                    ->whereNotNull('oic.contractor_id')
                    ->whereIn('o.orders_status_id', self::busyOrderStatus)
                    ->where('oi.item_id', $value['id'])
                    ->where('oi.orders_id', '!=', $id)
                    ->whereRaw('(STR_TO_DATE(o.installdate, "%Y-%c-%d") <= "'.$to.'") AND (STR_TO_DATE(o.enddate, "%Y-%c-%d") >= "'.$from.'")')
                    ->get();

                foreach ($orders as $v) {

                    $v = (array) $v;
                    $loopdate = strtotime( $v['installdate'] );
                    $enddate = strtotime( $v['enddate'] );

                    while( $loopdate <= $enddate ) {

                        if ($loopdate >= $fromtime && $loopdate <= $totime) {
                            $t = $value['id'].'_'.$loopdate;

                            if (isset($value['busy'][$t])) {
                                $value['busy'][$t]['orders'][] = ['id' => $v['orders_id'], 'status' => $v['orders_status_id']];
                                $value['busy'][$t]['amount'] += $v['quantity'];
                            } else {
                               $value['busy'][$t] = array(
                                    'date' => date('Y-m-d', $loopdate),
                                    'amount' => $v['quantity'],
                                    'orders' => [['id' => $v['orders_id'], 'status' => $v['orders_status_id']]],
                               );
                           }
                       }

                       $loopdate = strtotime( '+1 day', $loopdate );
                    }
                }

                $complects = $itemObject->prepare(DB::table('item_complect as ic')
                    ->select([
                            'ic.item_id as id',
                            'ic.quantity',
                            'i.typename',
                            'i.itemtype',
                            'i.article as art',
                            'i.availablecount as available',
                            'i.repair',
                            'i.stelag',
                            'ii.image'
                            ])
                    ->leftJoin('item as i',function($join) {
                         $join->on('i.item_id', '=', 'ic.item_id');
                         $join->on('i.cmf_site_id', DB::raw(1));
                     })
                    ->leftJoin('item_image as ii',function($join) {
                         $join->on('ii.item_id', '=', 'i.item_id');
                         $join->on('ii.cmf_site_id', '=', 'i.cmf_site_id');
                         $join->on('ii.ordering', DB::raw(1));
                     })
                    ->where('ic.status', '>', DB::raw(0))
                    ->where('ic.parent_id', $value['id'])
                    ->get());

                if ($complects) {

                    foreach ($complects as &$complect) {
                        $complect = (array) $complect;
                        $complect['ordered'] = $complect['quantity'] * $value['amount'];
                        if (!isset($result['accessories'][$complect['id']])) {
                            $result['accessories'][$complect['id']] = $complect;
                        
                            $pcomplect = &$result['accessories'][$complect['id']];
                            $pcomplect['parents'][] = ['art' => $value['art'], 'name' => $value['name'], 'type' => $value['type'], 'amount' => $complect['ordered']];

                            $orders = DB::table('orders as o')
                                ->select([
                                        'o.orders_id', 'o.installdate', 'o.enddate', 'o.orders_status_id', 's.quantity'
                                        ])
                                ->leftJoin(DB::raw('(select orders_id, sum(oi.quantity*ic.quantity) as quantity 
                                    from `orders_item` as oi left join `item_complect` as ic 
                                    on oi.item_id = ic.parent_id and ic.status = 1 and ic.item_id = ? where ic.item_id group by orders_id) as s')
                                    , 's.orders_id', '=', 'o.orders_id')
                                ->addBinding([$complect['id']])
                                ->whereIn('o.orders_status_id', self::busyOrderStatus)
                                ->where('o.orders_id', '!=', $id)
                                ->where('s.quantity', '>', 0)
                                ->whereRaw('(STR_TO_DATE(o.installdate, "%Y-%c-%d") <= "'.$to.'") AND (STR_TO_DATE(o.enddate, "%Y-%c-%d") >= "'.$from.'")')
                                ->orderBy('o.installdate')
                                ->get();

                            foreach ($orders as $v) {
                                $v = (array) $v;
                                $loopdate = max($fromtime, strtotime( $v['installdate'] ));
                                $enddate = min($totime, strtotime( $v['enddate'] ));
                                while( $loopdate <= $enddate ) {
                                    $t = $complect['id'].'_'.$loopdate;
                                    if (isset($pcomplect['busy'][$t])) {
                                        $pcomplect['busy'][$t]['orders'][$v['orders_id']] = ['id' => $v['orders_id'], 'status' => $v['orders_status_id']];
                                        $pcomplect['busy'][$t]['amount'] += $v['quantity'];
                                    } else {
                                       $pcomplect['busy'][$t] = array(
                                            'date' => date('Y-m-d', $loopdate),
                                            'amount' => $v['quantity'],
                                            'orders' => [$v['orders_id'] => ['id' => $v['orders_id'], 'status' => $v['orders_status_id']]],
                                       );
                                   }
                                   $loopdate = strtotime( '+1 day', $loopdate );
                               }
                            }
                        } else {
                            $result['accessories'][$complect['id']]['ordered'] += $complect['ordered'];
                            $result['accessories'][$complect['id']]['parents'][] = ['art' => $value['art'], 'name' => $value['name'], 'type' => $value['type'], 'amount' => $complect['ordered']];
                        }
                    }
                    unset($complect);
                }

                if (isset($value['busy'])) {
                    $value['busy'] = array_values($value['busy']);
                }

                $result['products'][$key] = $value;
            }

            $result['accessories'] = array_values($result['accessories']);

            foreach ($result['accessories'] as &$value) {

                if (isset($value['busy'])) {
                    $value['busy'] = array_values($value['busy']);

                    foreach ($value['busy'] as &$value2) {
                        $value2['orders'] = array_values($value2['orders']);
                    }

                    unset($value2);
                }
            }

            unset($value);
        } else {
            $result = null;
        }
        //unset($value);

        return $result;
    }

    /**
     * @param $options
     * @return \Illuminate\Database\Query\Builder
     */
    public function getList($options)
    {
        $fields = [
                    'o.orders_id as id',
                    'o.operator_id as managerId',
                    'o.orders_status_id as status',
                    'o.amount as price',
                    'o.date_insert as ordered',
                    'o.installdate as installDate',
                    'o.enddate as uninstallDate',
                    'o.response_time as responseTime',
                    'o.order_cancel_reason_id as declineId',
                    'o.companyname as company',
                    'o.ocenka as rating',
                    'osc.comment as description',
                    //'c.status as customer_status',
                ];

        if (isset($options['fields']) && $options['fields']) {
            $fields = array_merge($fields, $options['fields']);
        }

        $sql = DB::table('orders as o')
            ->select($fields)
            ->leftJoin('orders_status_comment as osc',function($join)
                         {
                             $join->on('osc.orders_id', '=', 'o.orders_id');
                             $join->on('osc.orders_status_id', '=', 'o.orders_status_id');
                         })
            ->orderBy(isset($options['sortType']) ? $options['sortType'] : 'installdate', isset($options['sortDir']) ? $options['sortDir'] : 'desc');

        if (isset($options['fields']) && $options['fields']) {
            $sql->leftJoin('user as customer', 'customer.user_id', '=', 'o.user_id')
                ->leftJoin(DB::raw('(select user_id
                        , count(DISTINCT orders.orders_id) as ordered
                        , sum(if(orders.orders_status_id = 3 or orders.orders_status_id = 9, 1, 0)) as done 
                    from `orders` group by user_id) as s'), 's.user_id', '=', 'o.user_id');
        }

        if (isset($options['manager']) && $options['manager']) {
            $sql->where('o.operator_id', $options['manager']);
        }

        if (isset($options['pid']) && $options['pid']) {
            $sql->where('o.stage_id', $options['pid']);
        }

        if (isset($options['user']) && $options['user']) {
            $sql->where('o.user_id', $options['user']);
        }

        if (isset($options['dateFrom']) && $options['dateFrom']) {
            $sql->whereRaw('STR_TO_DATE(date_insert, \'%Y-%m-%d\') >=?', [$options['dateFrom']]);
        }

        if (isset($options['dateTo']) && $options['dateTo']) {
            $sql->whereRaw('STR_TO_DATE(date_insert, \'%Y-%m-%d\') <=?', $options['dateTo']);
        }

        if (isset($options['installDateFrom']) && $options['installDateFrom']) {
            $sql->whereRaw('STR_TO_DATE(installdate, \'%Y-%m-%d\') >=?', [$options['installDateFrom']]);
        }

        if (isset($options['installDateTo']) && $options['installDateTo']) {
            $sql->whereRaw('STR_TO_DATE(installdate, \'%Y-%m-%d\') <=?', [$options['installDateTo']]);
        }

        if (isset($options['uninstallDateFrom']) && $options['uninstallDateFrom']) {
            $sql->whereRaw('STR_TO_DATE(enddate, \'%Y-%m-%d\') >=?', [$options['uninstallDateFrom']]);
        }

        if (isset($options['uninstallDateTo']) && $options['uninstallDateTo']) {
            $sql->whereRaw('STR_TO_DATE(enddate, \'%Y-%m-%d\') <=?', [$options['uninstallDateTo']]);
        }

        if (isset($options['query']) && ($query = $options['query'])) {
            $sql->whereRaw(
                "
                (c.name like '%$query%' or c.secondname like '%$query%' or c.lastname like '%$query%' or c.email like '%$query%' or c.phone like '%$query%' or o.orders_id = '$query')
            "
            );
        }

        if (isset($options['status']) && $options['status']) {
            $sql->whereIn('o.orders_status_id', $options['status']);
        }

        if (isset($options['excludeStatus']) && $options['excludeStatus']) {
            $sql->whereNotIn('o.orders_status_id', $options['excludeStatus']);
        }

        if (isset($options['managersID']) && !empty($options['managersID'])) {
            $managersID = json_decode($options['managersID'], true);
            $sql->whereIn('operator_id', $managersID);
        }

        if (isset($options['item_id'])) {
            $sql->where('item_id', $options['item_id']);
        }

        return $sql;
    }

    public function itemConvert($items)
    {
        $result = [];

        if (isset($items[0])) {
            $keys = array_keys((array)$items[0]);

            $customerKeys = array_filter($keys, function($k) {
                    return substr($k, 0, 8) === 'customer';
                });

            $customerKeys = array_combine(
                array_map(function($k){ return lcfirst(str_replace('customer', '', $k)); }, $customerKeys),
                $customerKeys
            );

            foreach ($items as $item) {
                $item = (array)$item;
                $i = array_filter($item, function($k) use($customerKeys) {
                        return !in_array($k, $customerKeys);
                    }, ARRAY_FILTER_USE_KEY);

                foreach ($customerKeys as $k=>$v) {
                    $i['customer'][$k] = $item[$v];
                }

                $result[] = $i;
            }
        }

        return $result;
    }

    public function ordersItem()
    {
        return $this->hasMany(OrdersItem::class, 'orders_id', 'orders_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function getItemOrdersByInstallDate($itemID, $installDate, $uninstallDate, $excludeOrders = [])
    {
        $orderQuery = DB::table('orders as o')
            ->select([
                'o.orders_id', 'o.orders_status_id', 'o.installdate', 'o.enddate', 'oi.quantity'
            ])
            ->leftJoin('orders_item as oi',function($join) {
                $join->on('oi.orders_id', '=', 'o.orders_id');
            })
            ->whereNotNull('o.installdate')
            ->whereIn('o.orders_status_id', [6,10,11,12,7,8,16,17,18,21])
            ->where('oi.item_id', $itemID)
            ->whereRaw('(STR_TO_DATE(o.installdate, "%Y-%c-%d") <= "'.$uninstallDate.'") AND (STR_TO_DATE(o.enddate, "%Y-%c-%d") >= "'.$installDate.'")');
        if (!empty($excludeOrders)) {
            $orderQuery->whereNotIn('oi.orders_id', $excludeOrders);
        }
        return $orderQuery->get();
    }

    public function getItemOrdersGroupByDate(int $itemID, $filters)
    {
        $fromtime = $totime = null;

        if (isset($filters['installDateFrom']) && !empty($filters['installDateFrom'])) {
            $fromtime = strtotime(str_replace('.', '-', $filters['installDateFrom']));
            $filters['uninstallDateFrom'] = $filters['installDateFrom'];
            unset($filters['installDateFrom']);
        }
        else if (isset($filters['uninstallDateFrom']) && !empty($filters['uninstallDateFrom'])) {
            $fromtime = strtotime(str_replace('.', '-', $filters['uninstallDateFrom']));
        }

        if (isset($filters['installDateTo']) && !empty($filters['installDateTo'])) {
            $totime = strtotime(str_replace('.', '-', $filters['installDateTo']));
        }
        else if (isset($filters['uninstallDateTo']) && !empty($filters['uninstallDateTo'])) {
            $totime = strtotime(str_replace('.', '-', $filters['uninstallDateTo']));
        }

        if (!$fromtime || !$totime) {
            return false;
        }

        $orders = OrderSearch::apply($filters)
            ->select([
                'orders.orders_id', 'orders_status_id', 'installdate', 'enddate', 'oi.quantity'
            ])
            ->leftJoin('orders_item as oi',function($join) {
                $join->on('oi.orders_id', '=', 'orders.orders_id');
            })
            ->where('orders.cmf_site_id', config('common.siteId', 1))
            ->where('oi.item_id', $itemID)
            ->get()
        ->toArray();

        $result = [];

        foreach ($orders as $v) {

            $v = (array) $v;
            $loopdate = strtotime( $v['installdate'] );
            $enddate = strtotime( $v['enddate'] );

            while( $loopdate <= $enddate) {

                if ($loopdate >= $fromtime && $loopdate <= $totime) {

                    $t = $itemID . '_' . $loopdate;
                    $order_id = $v['orders_id'];

                    if (isset($result[$t])) {

                        if (empty( array_filter($result[$t]['orders'], function ($item) use($order_id){
                            return $item['id'] == $order_id;
                        }))){
                            $result[$t]['orders'][] = ['id' => $v['orders_id'], 'status' => $v['orders_status_id']];
                            $result[$t]['amount'] += $v['quantity'];
                        }


                    } else {
                        $result[$t] = array(
                            'date' => date('Y-m-d', $loopdate),
                            'amount' => $v['quantity'],
                            'orders' => [['id' => $v['orders_id'], 'status' => $v['orders_status_id']]],
                        );
                    }
                }

                $loopdate = strtotime('+1 day', $loopdate);
            }
        }
            return array_values($result);

    }


    public static function getOrderById($id)
    {

        $result = (array) DB::table('orders as o')
            ->select(['hour_rent_per_day', 'orders_id', 'orders_status_id', 'user_cashback'])
            ->where('o.orders_id', $id)
            ->first();

        $result['item'] = DB::table('orders_item as oi')
            ->select([
                'i.alias', 'i.typename', 'i.itemtype', 'i.article', 'i.stelag', 'i.pricebuy',
                'oi.item_id',
                'ii.image',
                'oi.quantity',
                'oi.price',
                'oi.full_price',
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
                 $join->on('i.cmf_site_id', DB::raw(1));
             })
            ->leftJoin('item_image as ii',function($join) {
                 $join->on('ii.item_id', '=', 'i.item_id');
                 $join->on('ii.cmf_site_id', '=', 'i.cmf_site_id');
                 $join->on('ii.ordering', DB::raw(1));
             })
            ->where('oi.orders_id', $id)
            ->get();
        $result['total'] = $result['total_wd'] = 0;

        $result['pledge'] = DB::table('orders_item as oi')
                ->select([
                        DB::raw('SUM(`oic`.`deposit`) as pledge')
                        ])
                ->leftJoin('orders_item_contractor as oic',function($join) {
                     $join->on('oic.orders_item_id', '=', 'oi.orders_item_id');
                 })
                ->where('oi.orders_id', $id)
                ->where('oic.status', DB::raw(1))
                ->value('pledge');

        foreach ($result['item'] as &$value) {

            $value->day_price = $value->day_price_wd*(100 - $value->discount_percent_from_manager)/100;
            $result['total'] += ceil($value->quantity * $value->discountedPrice * $value->coefficient);
            $result['total_wd'] += ceil($value->quantity * $value->full_price * $value->coefficient);

            if (! isset($value->image)) {

                $value->image = DB::table('item_image as ii')
                    ->select(['image'])
                    -> where('ii.cmf_site_id', 1)
                    -> where('ii.item_id', DB::raw($value->item_id))
                    -> orderBy('ii.ordering')
                    -> first()->image;
            }
        }

        unset($value);

        $result['availableCashback'] =  ($result['user_cashback'] ? $result['total'] *$result['user_cashback'] / 100 : 0);

        return $result;
    }


    public function getStat($options = array())
    {
        $sql = DB::table('orders as o')
            ->select([
                'o.operator_id as managerId',
                DB::raw('count(1) as orders'),
                DB::raw('sum(if(o.orders_status_id IN (3, 9, 20, 26), 1, 0)) as done'),
                DB::raw('sum(if(o.orders_status_id IN (3, 9, 20, 26), o.rentprice + o.delivery + o.rentpersonal, 0)) as fact'),
                DB::raw('sum('.(isset($options['status']) ? 
                    'if(o.orders_status_id IN ('.implode(',', $options['status']).'), o.delivery, 0)' : 'o.delivery').') as innerDelivery'),
                DB::raw('sum('.(isset($options['status']) ? 
                    'if(o.orders_status_id IN ('.implode(',', $options['status']).'), o.rentpersonal, 0)' : 'o.rentpersonal').') as innerPersonnel'),
                DB::raw('sum('.(isset($options['status']) ? 
                    'if(o.orders_status_id IN ('.implode(',', $options['status']).'), 1, 0)' : '1').') as count'),
                DB::raw('sum('.(isset($options['status']) ? 
                    'if(o.orders_status_id IN ('.implode(',', $options['status']).'), o.rentprice, 0)' : 'o.rentprice').') as price'),
                DB::raw('sum(if(o.orders_status_id IN (3, 9, 20, 26), o.rentprice + o.procatilo, 0)) as _gprice'),
                DB::raw('sum(o.rentprice + o.procatilo) as _cprice'),
                DB::raw('sum(if(o.response_time, 1, 0)) as _gtime'),
                DB::raw('sum(if(o.response_time, substr(o.response_time, -2, 2)+substr(o.response_time, -5, 2)*60+SUBSTRING_INDEX(o.response_time, ":", 1)*60*60, 0)) as _ctime'),
                DB::raw('sum('.(isset($options['status']) ? 
                    'if(o.orders_status_id IN ('.implode(',', $options['status']).'), o.procatilo, 0)' : 'o.procatilo').') as rollup'),
                's.plan',
            ])
            ->leftJoin(DB::raw('(select * from `orders_plan` where 1=1'.
                (isset($options['dateFrom']) && $options['dateFrom'] ? ' and `date`>="'.$options['dateFrom'].'"' : '').
                (isset($options['dateTo']) && $options['dateTo'] ? ' and `date`<"'.$options['dateTo'].'"' : '').' group by user_id) as s'), 's.user_id', '=', 'o.operator_id')
            ->where(function ($query) {
                $query->whereNull('o.duplicate_number')
                    ->orWhere('o.duplicate_number', 0);
            })
            ->groupBy(['o.operator_id']);

        if (isset($options['managerId']) && is_array($options['managerId']) && count($options['managerId'])) {
            $sql->whereIn('o.operator_id', $options['managerId']);
        }

        if (isset($options['dateFrom']) && $options['dateFrom']) {
            $sql->whereRaw('STR_TO_DATE(o.installdate, \'%Y-%m-%d\') >=?', $options['dateFrom']);
        }

        if (isset($options['dateTo']) && $options['dateTo']) {
            $sql->whereRaw('STR_TO_DATE(o.installdate, \'%Y-%m-%d\') <=?', $options['dateTo']);
        }

        $result = $sql->get();
        $summary = ['innerRent' => 0, 'outerRent' => 0, 'innerService' => 0
        , 'outerService' => 0, 'rollup' => 0, 'fact' => 0, 'delivery' => 0, 'install' => 0];

        foreach ($result as &$value) {

            $sql = DB::table('orders as o')
                ->select([
                    DB::raw('sum(if(oic.contractor_id <> 3 and oic.status = 1'.(isset($options['status']) ?
                        ' and o.orders_status_id IN ('.implode(',', $options['status']).')' : '').', oic.price * oic.quantity, 0)) as outerRent'),
                    DB::raw('sum(if(oic.contractor_id <> 3 and oic.status = 1'.(isset($options['status']) ?
                        ' and o.orders_status_id IN ('.implode(',', $options['status']).')' : '').', ifnull(oic.price_delivery, 0) + ifnull(oic.price_montag, 0), 0)) as outerService'),
                ])
                ->leftJoin('orders_item as oi',function($join) {
                     $join->on('oi.orders_id', '=', 'o.orders_id');
                 })
                ->leftJoin('item as i',function($join) {
                     $join->on('oi.item_id', '=', 'i.item_id');
                 })
                ->leftJoin('orders_item_contractor as oic',function($join) {
                     $join->on('oic.orders_item_id', '=', 'oi.orders_item_id');
                 })
                ->where(function ($query) {
                    $query->whereNull('o.duplicate_number')
                        ->orWhere('o.duplicate_number', 0);
                })
                ->where('o.operator_id', $value->managerId)
                ->groupBy(['o.operator_id']);

            if (isset($options['dateFrom']) && $options['dateFrom']) {
                $sql->whereRaw('STR_TO_DATE(o.installdate, \'%Y-%m-%d\') >=?', $options['dateFrom']);
            }

            if (isset($options['dateTo']) && $options['dateTo']) {
                $sql->whereRaw('STR_TO_DATE(o.installdate, \'%Y-%m-%d\') <=?', $options['dateTo']);
            }

            $extraData = $sql->first();

            $value->cleanPrice = $value->price - $extraData->outerRent;
            $value->outerRent = $extraData->outerRent;
            $value->outerService = $extraData->outerService;
            $value->charge = $value->price + $value->rollup;
            $value->chargePercent = $value->charge ? round(($value->charge - $value->outerRent) / $value->charge * 100, 2) : 0;
            $value->ordersConversion = $value->orders ? round($value->done / $value->orders * 100, 2) : 0;
            $value->moneyConversion = $value->_cprice ? round($value->_gprice / $value->_cprice * 100, 2) : 0;
            $value->average = $value->count ? round($value->charge / $value->count) : 0;
            $time = $value->_gtime ? round($value->_ctime / $value->_gtime) : 0;
            $value->responseTime = sprintf('%02d:%02d:%02d', $time/3600, ($time % 3600)/60, ($time % 3600) % 60);
            $summary['innerRent'] += $value->cleanPrice;
            $summary['outerRent'] += $value->outerRent;
            $summary['delivery'] += $value->innerDelivery;
            $summary['install'] += $value->innerPersonnel;
            $summary['innerService'] += $value->innerDelivery + $value->innerPersonnel;
            $summary['outerService'] += $value->outerService;
            $summary['rollup'] += $value->rollup;
            $summary['fact'] += $value->fact;
        }

        $summary['rent'] = $summary['innerRent'] + $summary['outerRent'];
        $summary['total'] = $summary['rent'] + $summary['delivery'] + $summary['install'];

        unset($value);

        $sql = DB::table('orders_plan as op')
            ->select(['op.plan', 'op.payback_point as paybackPoint'])
            ->where(function ($query) {
                $query->whereNull('op.user_id')
                    ->orWhere('op.user_id', 0);
            })
            ->orderBy('op.date');

        if (isset($options['dateFrom']) && $options['dateFrom']) {
            $sql->whereRaw('`date` >=?', $options['dateFrom']);
        }

        if (isset($options['dateTo']) && $options['dateTo']) {
            $sql->whereRaw('`date` <= ?', $options['dateTo']);
        }

        $plan = ['plan' => null, 'paybackPoint' => null, 'fact' => $summary['fact']];

        if ($planObject = $sql->first()) {
            $plan['plan'] = $planObject->plan;
            $plan['paybackPoint'] = $planObject->paybackPoint;
        }

        return ['managers' => $result, 'summary' => $summary, 'plan' => $plan];
    }


    public static function getStatForOrderQuery($query)
    {
        $result = $query
            ->select(
                [
                    DB::raw('sum(orders.rentprice) as rent'),
                    DB::raw('sum(orders.delivery + orders.rentpersonal) as innerService'),
                    DB::raw('sum(if(oic.contractor_id <> 3 and oic.status = 1, oic.price * oic.quantity, 0)) as outerRent'),
                    DB::raw('sum(if(oic.contractor_id <> 3 and oic.status = 1, ifnull(oic.price_delivery, 0) + ifnull(oic.price_montag, 0), 0)) as outerService'),
                    DB::raw('sum(orders.procatilo) as rollup'),
                ])
            ->leftJoin('orders_item as oi',function($join) {
                 $join->on('oi.orders_id', '=', 'orders.orders_id');
             })
            ->leftJoin('item as i',function($join) {
                 $join->on('oi.item_id', '=', 'i.item_id');
             })
            ->leftJoin('orders_item_contractor as oic',function($join) {
                 $join->on('oic.orders_item_id', '=', 'oi.orders_item_id');
             })
            ->where(function ($query) {
                $query->whereNull('orders.duplicate_number')
                    ->orWhere('orders.duplicate_number', 0);
            })->first();
        if ($result) {

            $result->innerRent = $result->rent - $result->outerRent;
        }

        return $result;
    }
}
