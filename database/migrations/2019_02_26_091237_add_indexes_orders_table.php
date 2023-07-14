<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index('operator_id');
            $table->index('orders_status_id');
            $table->index('enddate');
            $table->index('installdate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('operator_id');
            $table->dropIndex('enddate');
            $table->dropIndex('orders_status_id');
            $table->dropIndex('installdate');
        });
    }
}
