<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersStatusLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders_status_log', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('status_id')->nullable();
            $table->unsignedInteger('orders_id')->nullable();
            $table->dateTime('cdate')->nullable();
            $table->index(['user_id']);
            $table->index(['status_id']);
            $table->index(['orders_id']);
        });

        DB::statement("INSERT IGNORE INTO `cmf_script` (`cmf_site_id`, `cmf_script_id`, `parent_id`, `article`, `name`, `url`, `description`, `status`, `realstatus`, `ordering`, `lastnode`, `tabname`, `modelname`, `is_group_node`, `is_new_win`, `catname`, `realcatname`, `is_protected`, `is_exclude_path`, `is_search`, `form_id`) VALUES
(1, 480,    275,    'OrdersStatusLog',  'Лог смены статуса',    '/admin/OrdersStatusLog/',  '', 0,  0,  7,  1,  '', 'OrdersStatusLog',  '0',    0,  '', '1/13/273/275/480', 1,  0,  0,  NULL)");
        DB::statement("INSERT IGNORE INTO `module_scripts` (`module_id`, `cmf_script_id`) VALUES
(1, 480),
(17,    480)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders_status_log');
    }
}
