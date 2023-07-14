<?php
namespace App\GraphQL\Type;

use Rebing\GraphQL\Support\Facades\GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use App\Order;
use Illuminate\Support\Facades\DB;

class OrderType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Order',
        'description' => 'A order',
        'model' => Order::class,
    ];

    public function fields(): array
    {

        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the order',
                'alias' => 'orders_id'
            ],
            'status' => [
                'type' => GraphQL::type('OrderStatus'),
                'description' => 'The status of order',
            ],
            'cmfSite' => [
                'type' => GraphQL::type('CmfSite'),
                'description' => 'The cmfSite of order',
            ],
            'eventType' => [
                'type' => GraphQL::type('OrderEventType'),
                'description' => 'The event type of order',
            ],
            'log' => [
                'type' => Type::nonNull(Type::listOf(GraphQL::type('OrderStatusLog'))),
                'description' => 'The status log of order',
            ],
            'logist' => [
                'type' => Type::listOf(GraphQL::type('OrderLogist')),
            ],
            'user' => [
                'type' => GraphQL::type('User'),
                'description' => 'The manager of order',
            ],
            'manager' => [
                'type' => GraphQL::type('User'),
                'description' => 'The manager of order',
            ],
            'price' => [
                'type' => Type::string(),
                'description' => 'The price of order',
                'alias' => 'amount',
            ],
            'done' => [
                'type' => Type::string(),
                'description' => 'The done orders for this customer'
            ],
            'company' => [
                'type' => Type::string(),
                'description' => 'The company for order',
                'alias' => 'companyname'
            ],
            'rating' => [
                'type' => Type::string(),
                'description' => 'The rating for order',
                'alias' => 'ocenka'
            ],
            'feedback' => [
                'type' => Type::string(),
                'description' => 'The feedback for order'
            ],
            'feedbackAnswer' => [
                'type' => Type::string(),
                'description' => 'The feedback answer for order'
            ],
            'rollup' => [
                'type' => Type::string(),
                'description' => 'The rollup of order',
                'alias' => 'procatilo',
            ],
            'finalized' => [
                'type' => Type::boolean(),
                'description' => 'The rollup of order',
            ],
            'ordered' => [
                'type' => Type::string(),
                'description' => 'The date insert of order',
                'alias' => 'date_insert',
            ],
            'responseTime' => [
                'type' => Type::string(),
                'description' => 'The response time of order',
                'alias' => 'response_time',
            ],
            'installDate' => [
                'type' => Type::string(),
                'description' => 'The install date of order',
                'alias' => DB::raw('concat(orders.installdate, " ", orders.installtime) as installDate'),
            ],
            'uninstallDate' => [
                'type' => Type::string(),
                'description' => 'The uninstall date of order',
                'alias' => DB::raw('concat(orders.enddate, " ", orders.endtime) as uninstallDate'),
            ],
            'rent' => [
                'type' => Type::string(),
                'description' => 'The rent of order',
                'alias' => 'rentprice',
            ],
            'innerService' => [
                'type' => Type::string(),
                'description' => 'The inner service of order',
                'alias' => DB::raw('(orders.delivery + orders.rentpersonal) as innerService'),
            ],
            'innerServiceConsumption' => [
                'type' => Type::string(),
                'description' => 'The inner service of order',
                'alias' => DB::raw('(orders.rentpersonal_internal_our + orders.rentpersonal_internal_their + orders.delivery_internal_our + orders.delivery_internal_their + ifnull(orders.taxiConsumption,0)) as innerServiceConsumption'),
            ],
            'callDate' => [
                'type' => Type::string(),
                'description' => 'The call date of order',
                'alias' => 'call_date',
            ],
            'bookingDate' => [
                'type' => Type::string(),
                'description' => 'The reserv time of order',
                'alias' => 'reserv_time',
            ],
            'paymentDate' => [
                'type' => Type::string(),
                'description' => 'The payment date of order',
                'alias' => 'payment_date',
            ],
            'description' => [
                'type' => Type::string(),
                'description' => 'The description of order',
            ],
            'paymentSum' => [
                'type' => Type::string(),
                'description' => 'The prepayment of order',
                'alias' => 'prepayment'
            ],
            'isPledgeReceived' => [
                'type' => Type::boolean(),
                'description' => 'The deposit received of order',
                'alias' => 'deposit_received'
            ],
            'isPledgeReturned' => [
                'type' => Type::boolean(),
                'description' => 'The deposit returned of order',
                'alias' => 'deposit_returned'
            ],
            'isPledgeReturned' => [
                'type' => Type::boolean(),
                'description' => 'The deposit returned of order',
                'alias' => 'deposit_returned'
            ],
            'paymentStatus' => [
                'type' => GraphQL::type('OrderPaymentStatus'),
                'description' => 'The payment status of order',
            ],
            'price' => [
                'type' => Type::string(),
                'description' => 'The price of order',
                'alias' => 'amount',
            ],
            'paymentSum' => [
                'type' => Type::string(),
                'description' => 'The paymentSum of order',
                'alias' => 'prepayment',
            ],
            'mkadDistance' => [
                'type' => Type::string(),
                'description' => 'The mkadDistance of order',
                'alias' => 'mkadkm',
            ],
            'taxiConsumption' => [
                'type' => Type::string(),
                'description' => 'The taxiConsumption of order',
            ],
            'installResponsible' => [
                'type' => Type::string(),
                'description' => 'The installResponsible of order',
                'alias' => 'contact_name',
            ],
            'installResponsiblePhone' => [
                'type' => Type::string(),
                'description' => 'The installResponsiblePhone of order',
                'alias' => 'contact_phone',
            ],
            'innerCars' => [
                'type' => Type::string(),
                'description' => 'The innerCars of order',
                'alias' => 'car_amount_our',
            ],
            'cars' => [
                'type' => Type::string(),
                'description' => 'The cars of order',
                'alias' => 'car_amount',
            ],
            'outerCars' => [
                'type' => Type::string(),
                'description' => 'The outerCars of order',
                'alias' => 'car_amount_their',
            ],
            'innerPersonnel' => [
                'type' => Type::string(),
                'description' => 'The innerPersonnel of order',
                'alias' => 'worker_amount_our',
            ],
            'personnel' => [
                'type' => Type::string(),
                'description' => 'The personnel of order',
                'alias' => 'worker_amount',
            ],
            'outerPersonnel' => [
                'type' => Type::string(),
                'description' => 'The outerPersonnel of order',
                'alias' => 'worker_amount_their',
            ],
            'deliveryPrice' => [
                'type' => Type::string(),
                'description' => 'The deliveryPrice of order',
                'alias' => 'delivery',
            ],
            'innerDeliveryPrice' => [
                'type' => Type::string(),
                'description' => 'The innerDeliveryPrice of order',
                'alias' => 'delivery_internal_our',
            ],
            'outerDeliveryPrice' => [
                'type' => Type::string(),
                'description' => 'The outerDeliveryPrice of order',
                'alias' => 'delivery_internal_their',
            ],
            'installPrice' => [
                'type' => Type::string(),
                'description' => 'The installPrice of order',
                'alias' => 'rentpersonal',
            ],
            'innerInstallPrice' => [
                'type' => Type::string(),
                'description' => 'The innerInstallPrice of order',
                'alias' => 'rentpersonal_internal_our',
            ],
            'outerInstallPrice' => [
                'type' => Type::string(),
                'description' => 'The outerInstallPrice of order',
                'alias' => 'rentpersonal_internal_their',
            ],
            'rollup' => [
                'type' => Type::string(),
                'description' => 'The rollup of order',
                'alias' => 'procatilo',
            ],
            'foreignDelivery' => [
                'type' => Type::boolean(),
                'description' => 'The foreignDelivery of order',
                'alias' => 'foreign_delivery',
            ],
            'selfDelivery' => [
                'type' => Type::boolean(),
                'description' => 'The selfDelivery of order',
                'alias' => 'is_pickup',
            ],
            'decline' => [
                'type' => GraphQL::type('OrderCancelReason'),
                'description' => 'The decline of order',
            ],
            'noteLogistician' => [
                'type' => Type::string(),
                'description' => 'The noteLogistician of order',
                'alias' => 'description_logist',
            ],
            'noteStock' => [
                'type' => Type::string(),
                'description' => 'The noteStock of order',
                'alias' => 'description_warehouse',
            ],
            'noteDriver' => [
                'type' => Type::string(),
                'description' => 'The noteDriver of order',
                'alias' => 'description_driver',
            ],
            'noteManager' => [
                'type' => Type::string(),
                'description' => 'The noteManager of order',
                'alias' => 'description_manager',
            ],
            'noteCustomer' => [
                'type' => Type::string(),
                'description' => 'The noteCustomer of order',
                'alias' => 'description',
            ],
            'stage' => [
                'type' => Type::string(),
                'description' => 'The stage of order',
            ],
            'paymentType' => [
                'type' => Type::string(),
                'description' => 'The paymentType of order',
                'alias' => 'payment_type',
            ],
            'address' => [
                'type' => Type::string(),
                'description' => 'The address of order',
            ],
            'eventStart' => [
                'type' => Type::string(),
                'description' => 'The eventStart of order',
                'alias' => 'event_start',
            ],
            'eventEnd' => [
                'type' => Type::string(),
                'description' => 'The eventEnd of order',
                'alias' => 'event_finish',
            ],
            'daysChanged' => [
                'type' => Type::string(),
                'description' => 'The daysChanged of order',
                'alias' => 'rent_day',
            ],
            'hoursChanged' => [
                'type' => Type::string(),
                'description' => 'The hoursChanged of order',
                'alias' => 'hour_rent_per_day',
            ],
            'elevatorSizes' => [
                'type' => Type::string(),
                'description' => 'The elevatorSizes of order',
                'alias' => 'liftDimentions',
            ],
            'parking' => [
                'type' => Type::string(),
                'description' => 'The parking of order',
            ],
            'distance' => [
                'type' => Type::string(),
                'description' => 'The distance of order',
            ],
            'parkingPrice' => [
                'type' => Type::string(),
                'description' => 'The parkingPrice of order',
            ],
            'entryHeight' => [
                'type' => Type::string(),
                'description' => 'The entryHeight of order',
            ],
            'elevator' => [
                'type' => Type::string(),
                'description' => 'The elevator of order',
            ],
            'elevatorDistance' => [
                'type' => Type::string(),
                'description' => 'The elevatorDistance of order',
            ],
            'corridorWidth' => [
                'type' => Type::string(),
                'description' => 'The corridorWidth of order',
            ],
            'riseToFloor' => [
                'type' => Type::string(),
                'description' => 'The riseToFloor of order',
            ],
            'stepsWidth' => [
                'type' => Type::string(),
                'description' => 'The stepsWidth of order',
            ],
            'stepsTurnWidth' => [
                'type' => Type::string(),
                'description' => 'The stepsTurnWidth of order',
            ],
            'stageScheme' => [
                'type' => Type::string(),
                'description' => 'The stageScheme of order',
            ],
            'pass' => [
                'type' => Type::string(),
                'description' => 'The pass of order',
            ],
            'location' => [
                'type' => Type::string(),
                'description' => 'The location of order',
            ],
            'innerRent' => [
                'type' => Type::string(),
                'description' => 'The inner rent of order',
                'selectable' => false,
            ],
            'outerRent' => [
                'type' => Type::string(),
                'description' => 'The outer rent of order',
                'selectable' => false,
            ],
            'outerService' => [
                'type' => Type::string(),
                'description' => 'The inner service of order',
                'selectable' => false,
            ],
            'serviceProfit' => [
                'type' => Type::string(),
                'description' => 'The service profit of order',
                'selectable' => false,
            ],
            'installResponsible' => [
                'type' => Type::string(),
                'description' => 'The install contact name of order',
                'alias' => 'contact_name',
            ],
            'installResponsiblePhone' => [
                'type' => Type::string(),
                'description' => 'The install contact phone of order',
                'alias' => 'contact_phone',
            ],
            'uninstallResponsible' => [
                'type' => Type::string(),
                'description' => 'The uninstall contact name of order',
                'alias' => 'uninstall_responsible',
            ],
            'uninstallResponsiblePhone' => [
                'type' => Type::string(),
                'description' => 'The uninstall contact phone of order',
                'alias' => 'uninstall_responsible_phone',
            ],
            'statusDescription' => [
                'type' => Type::string(),
                'description' => 'The status description of order',
                'selectable' => false,
            ],
            'installDoneDate' => [
                'type' => Type::string(),
                'description' => 'The install done date of order',
                'selectable' => false,
            ],
            'uninstallDoneDate' => [
                'type' => Type::string(),
                'description' => 'The uninstall done date of order',
                'selectable' => false,
            ],
            'rentRefund' => [
                'type' => Type::string(),
                'description' => 'The rent refund of order',
                'alias' => 'rent_refund',
            ],
            'serviceRefund' => [
                'type' => Type::string(),
                'description' => 'The service refund of order',
                'alias' => 'service_refund',
            ],
            'rentRefundReason' => [
                'type' => GraphQL::type('OrderRentRefundReason'),
                'description' => 'The rent refund of order',
            ],
            'serviceRefundReason' => [
                'type' => GraphQL::type('OrderServiceRefundReason'),
                'description' => 'The service refund of order',
            ],
            'customerNeeds' => [
                'type' => GraphQL::type('OrderGoal'),
                'description' => 'The goal of order',
            ],
            'calculationDate' => [
                'type' => Type::string(),
                'description' => 'The settlement date',
                'alias' => 'settlement_date',
            ],
            'alternativesAgree' => [
                'type' => Type::string(),
                'description' => 'The alternative',
                'alias' => 'alternative',
            ],
            'products' => [
                'type' => Type::listOf(GraphQL::type('OrderProduct')),
                'description' => 'The products of order',
            ],
            'payments' => [
                'type' => Type::listOf(GraphQL::type('OrderPayment')),
                'description' => 'The payments of order',
            ],
            'proposals' => [
                'type' => Type::listOf(GraphQL::type('OrderProposal')),
                'description' => 'The proposals of order',
                'selectable' => false,
            ],
            'contractors' => [
                'type' => Type::listOf(GraphQL::type('Contractor')),
                'description' => 'The contractors of order',
                'selectable' => false,
            ],
            'payfromCashback' => [
                'type' => Type::string(),
                'description' => 'The payfromCashback of order',
                'selectable' => false,
            ],
            'amountForTax' => [
                'type' => Type::listOf(GraphQL::type('OrderAmountTax')),
                'description' => 'The amount tax',
                'selectable' => false,
            ],
            'userCategory' => [
                'type' => GraphQL::type('UserCategory'),
                'description' => 'The user categiry'
            ],
            'userCashback' => [
                'type' => Type::string(),
                'description' => 'The cashback of user for order',
                'alias' => 'user_cashback',
            ],
            'userPaymentCashback' => [
                'type' => Type::string(),
                'description' => 'The payfrom cashback of user for order',
                'alias' => 'user_payment_cashback',
            ],
            'userDiscount' => [
                'type' => Type::string(),
                'description' => 'The discount of user for order',
                'alias' => 'user_discount',
            ],
            'promocode' => [
                'type' => Type::string(),
                'description' => 'The promocode',
            ],
            'discountPromocode' => [
                'type' => Type::string(),
                'description' => 'The discount promocode',
                'alias' => 'discount_promocode',
            ],
            'cashbackPromocode' => [
                'type' => Type::string(),
                'description' => 'The cashback promocode',
                'alias' => 'cashback_promocode',
            ],
            'promocodeUser' => [
                'type' => GraphQL::type('User'),
                'description' => 'The promocode user of order',
            ],
            'documents' => [
                'type' => Type::listOf(GraphQL::type('OrderDocument')),
                'description' => 'The documents of order',
            ],
        ];
    }
}