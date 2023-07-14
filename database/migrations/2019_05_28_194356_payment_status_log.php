<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PaymentStatusLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('orders_payment_status_id')->nullable();
            $table->index(['orders_payment_status_id']);
        });

        Schema::create('orders_payment_status', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->unsignedInteger('ordering')->nullable();
        });

        Schema::create('orders_payment_status_log', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('status_id')->nullable();
            $table->unsignedInteger('orders_id')->nullable();
            $table->dateTime('cdate')->nullable();
            $table->index(['user_id']);
            $table->index(['status_id']);
            $table->index(['orders_id']);
        });
        DB::statement("INSERT INTO `orders_payment_status` (`id`, `name`, `ordering`) VALUES (1, 'Постоплата',   1),(2, 'Предоплата',   2),(4, 'Залог',    4)");

        DB::statement("INSERT INTO `cmf_script` (`cmf_site_id`, `cmf_script_id`, `parent_id`, `article`, `name`, `url`, `description`, `status`, `realstatus`, `ordering`, `lastnode`, `tabname`, `modelname`, `is_group_node`, `is_new_win`, `catname`, `realcatname`, `is_protected`, `is_exclude_path`, `is_search`, `form_id`) VALUES
(1, 481,    273,    'OrdersPaymentStatus',  'Статусы оплаты заказов',   '/admin/OrdersPaymentStatus/',  '', 1,  1,  13, 1,  '', 'OrdersPaymentStatus',  '0',    0,  '', '1/13/273/481', 1,  0,  0,  NULL),
(1, 482,    275,    'OrdersPaymentStatusLog',   'Лог смены статуса оплаты заказа',  '/admin/OrdersPaymentStatusLog/',   '', 1,  1,  8,  1,  '', 'OrdersPaymentStatusLog',   '0',    0,  '', '1/13/273/275/482', 1,  0,  0,  NULL)");
        DB::statement("INSERT IGNORE INTO `module_scripts` (`module_id`, `cmf_script_id`) VALUES (1, 481), (17, 481),(1, 482), (17, 482)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders_payment_status');
        Schema::dropIfExists('orders_payment_status_log');
    }

}
