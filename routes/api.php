<?php

use Illuminate\Http\Request;
use Laravel\Passport\Passport;
use Carbon\Carbon;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', ['middleware' => 'cors', 'uses' => 'API\UserController@login'])
    ->name('login');

Route::get('loginByEmail', ['middleware' => 'cors', 'uses' => 'API\UserController@loginByEmail']);

Route::post('register', 'API\UserController@register');

/*
 * User styles data
 */
Route::get('platform', ['middleware' => 'cors', 'uses' => 'API\PlatformController@index']);

Route::group(['middleware' => ['auth.cors']], function(){
    Route::get('order/statuses', 'API\OrderController@statuses');
    Route::get('driver/list', 'API\DriverController@list');
    Route::get('car/list', 'API\CarController@list');
    Route::get('user/helpers', 'API\UserController@helpers');

    /** ordersCancelReason */
    Route::get('ordersCancelReason', 'API\OrdersCancelReasonController@index');
    Route::get('ordersCancelReason/{id}', 'API\OrdersCancelReasonController@show');
    Route::post('ordersCancelReason', 'API\OrdersCancelReasonController@store');
    Route::put('ordersCancelReason/{id}', 'API\OrdersCancelReasonController@update');
    Route::delete('ordersCancelReason/{id}', 'API\OrdersCancelReasonController@delete');

    /** discounter */
    Route::get('discounter', 'API\DiscounterController@index');
    Route::get('discounter/{id}', 'API\DiscounterController@show');
    Route::post('discounter', 'API\DiscounterController@store');
    Route::put('discounter/{id}', 'API\DiscounterController@update');
    Route::delete('discounter/{id}', 'API\DiscounterController@delete');

    /** userStatus */
    Route::get('userStatus', 'API\UserStatusController@index');
    Route::get('userStatus/{id}', 'API\UserStatusController@show');
    Route::post('userStatus', 'API\UserStatusController@store');
    Route::put('userStatus/{id}', 'API\UserStatusController@update');
    Route::delete('userStatus/{id}', 'API\UserStatusController@delete');
});

Route::group(['middleware' => ['auth.cors', 'auth:api']], function(){
    Route::get('user/details', 'API\UserController@details');
    Route::get('user/managers', 'API\UserController@managers');
    Route::get('cmfrole/list', 'API\CmfRoleController@list');
    Route::get('user/logout', 'API\UserController@logout');
    Route::any('customer', 'API\UserController@customer');
    Route::any('customer/add', 'API\UserController@add');
    Route::get('customerOrders', 'API\OrderController@ordersForCustomer');
    Route::get('customers', 'API\UserController@customers');
    Route::get('customers/details', 'API\UserController@customerOrders');

    Route::get('equipment/items', 'API\EquipmentController@items');

    Route::get('order/list', 'API\OrderController@list');
    Route::get('order/ordersformanagerwithoutpagination', 'API\OrderController@ordersForManagerWithoutPagination');
    Route::get('order/ordersformanager', 'API\OrderController@ordersForManager');
    Route::get('order/ordersforpid', 'API\OrderController@ordersForPid');
    Route::get('order', 'API\OrderController@order');
    Route::get('managerOrder', 'API\OrderController@order');
    Route::post('managerOrder/update', 'API\OrderController@update');
    Route::get('addOrderFeedback', 'API\OrderController@update');
    Route::get('rubrics', 'API\CatalogueController@catalogue');
    Route::get('managerEquipment', 'API\CatalogueController@items');

    /** callType */
    Route::get('callType', 'API\CallTypeController@index');
    Route::get('callType/{id}', 'API\CallTypeController@show');
    Route::post('callType', 'API\CallTypeController@store');
    Route::put('callType/{id}', 'API\CallTypeController@update');
    Route::delete('callType/{id}', 'API\CallTypeController@delete');
    Route::post('callType/set', 'API\CallTypeController@setCallType');

    /** callRating */
    Route::get('callRating', 'API\CallRatingController@index');
    Route::get('callRating/{id}', 'API\CallRatingController@show');
    Route::post('callRating', 'API\CallRatingController@store');
    Route::put('callRating/{id}', 'API\CallRatingController@update');
    Route::delete('callRating/{id}', 'API\CallRatingController@delete');
    Route::post('callRating/set', 'API\CallRatingController@setRating');

    /** Item */
    Route::get('item/search', 'API\ItemController@search');
    Route::get('item', 'API\ItemController@index');
    Route::get('item/{id}', 'API\ItemController@show');
    Route::post('item', 'API\ItemController@store');
    Route::put('item/{id}', 'API\ItemController@update');
    Route::delete('item/{id}', 'API\ItemController@delete');
    Route::get('item/{id}/orders', 'API\ItemController@getOrders');

    /** Contractor */
    Route::get('contractor', 'API\ContractorController@index');
    Route::get('contractor/search', 'API\ContractorController@search');
    Route::get('contractor/{id}', 'API\ContractorController@show');
    Route::post('contractor', 'API\ContractorController@store');
    Route::put('contractor/{id}', 'API\ContractorController@update');
    Route::delete('contractor/{id}', 'API\ContractorController@delete');

    /** ItemCondition */
    Route::get('itemCondition', 'API\ItemConditionController@index');
    Route::get('itemCondition/{id}', 'API\ItemConditionController@show');
    Route::post('itemCondition', 'API\ItemConditionController@store');
    Route::put('itemCondition/{id}', 'API\ItemConditionController@update');
    Route::delete('itemCondition/{id}', 'API\ItemConditionController@delete');

    /** todoPriority */
    Route::get('todoPriority', 'API\TodoPriorityController@index');
    Route::get('todoPriority/{id}', 'API\TodoPriorityController@show');
    Route::post('todoPriority', 'API\TodoPriorityController@store');
    Route::put('todoPriority/{id}', 'API\TodoPriorityController@update');
    Route::delete('todoPriority/{id}', 'API\TodoPriorityController@delete');

    /** todoState */
    Route::get('todoState', 'API\TodoStateController@index');
    Route::get('todoState/{id}', 'API\TodoStateController@show');
    Route::post('todoState', 'API\TodoStateController@store');
    Route::put('todoState/{id}', 'API\TodoStateController@update');
    Route::delete('todoState/{id}', 'API\TodoStateController@delete');

    /** todoType */
    Route::get('todoType', 'API\TodoTypeController@index');
    Route::get('todoType/{id}', 'API\TodoTypeController@show');
    Route::post('todoType', 'API\TodoTypeController@store');
    Route::put('todoType/{id}', 'API\TodoTypeController@update');
    Route::delete('todoType/{id}', 'API\TodoTypeController@delete');

    /** todoStatus */
    Route::get('todoStatus', 'API\TodoStatusController@index');
    Route::get('todoStatus/{id}', 'API\TodoStatusController@show');
    Route::post('todoStatus', 'API\TodoStatusController@store');
    Route::put('todoStatus/{id}', 'API\TodoStatusController@update');
    Route::delete('todoStatus/{id}', 'API\TodoStatusController@delete');

    /** todoType */
    Route::get('todoType', 'API\TodoTypeController@index');
    Route::get('todoType/{id}', 'API\TodoTypeController@show');
    Route::post('todoType', 'API\TodoTypeController@store');
    Route::put('todoType/{id}', 'API\TodoTypeController@update');
    Route::delete('todoType/{id}', 'API\TodoTypeController@delete');

    /** todoState */
    Route::get('todoState', 'API\TodoStateController@index');
    Route::get('todoState/{id}', 'API\TodoStateController@show');
    Route::post('todoState', 'API\TodoStateController@store');
    Route::put('todoState/{id}', 'API\TodoStateController@update');
    Route::delete('todoState/{id}', 'API\TodoStateController@delete');

    /** todoManager */
    Route::get('todoManager', 'API\TodoManagerController@index');
    Route::get('todoManager/search', 'API\TodoManagerController@search');
    Route::get('todoManager/{id}', 'API\TodoManagerController@show');
    Route::post('todoManager', 'API\TodoManagerController@store');
    Route::put('todoManager/{id}', 'API\TodoManagerController@update');
    Route::delete('todoManager/{id}', 'API\TodoManagerController@delete');

    /** color */
    Route::get('color', 'API\ColorController@index');
    Route::get('color/{id}', 'API\ColorController@show');
    Route::post('color', 'API\ColorController@store');
    Route::put('color/{id}', 'API\ColorController@update');
    Route::delete('color/{id}', 'API\ColorController@delete');


    /** equipmentCondition */
    Route::get('equipmentCondition', 'API\EquipmentConditionController@index');
    Route::get('equipmentCondition/{id}', 'API\EquipmentConditionController@show');
    Route::post('equipmentCondition', 'API\EquipmentConditionController@store');
    Route::put('equipmentCondition/{id}', 'API\EquipmentConditionController@update');
    Route::delete('equipmentCondition/{id}', 'API\EquipmentConditionController@delete');

    /** equipmentMaterial */
    Route::get('equipmentMaterial', 'API\EquipmentMaterialController@index');
    Route::get('equipmentMaterial/{id}', 'API\EquipmentMaterialController@show');
    Route::post('equipmentMaterial', 'API\EquipmentMaterialController@store');
    Route::put('equipmentMaterial/{id}', 'API\EquipmentMaterialController@update');
    Route::delete('equipmentMaterial/{id}', 'API\EquipmentMaterialController@delete');

    /** OrdersPaymentStatus */
    Route::get('ordersPaymentStatus', 'API\OrdersPaymentStatusController@index');
    Route::get('ordersPaymentStatus/{id}', 'API\OrdersPaymentStatusController@show');
    Route::post('ordersPaymentStatus', 'API\OrdersPaymentStatusController@store');
    Route::put('ordersPaymentStatus/{id}', 'API\OrdersPaymentStatusController@update');
    Route::delete('ordersPaymentStatus/{id}', 'API\OrdersPaymentStatusController@delete');

    /** report */
    Route::get('report', 'API\ReportController@getStat');
    Route::get('report/warehouse', 'API\ReportController@getWarehouse');
    Route::post('report/warehouse', 'API\ReportController@updateWarehouse');

});