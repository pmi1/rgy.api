<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimestampsStatusLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('orders_status_log', function (Blueprint $table) {
            $table->timestamps();
        });
        Schema::table('orders_payment_status_log', function (Blueprint $table) {
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('orders_status_log', function (Blueprint $table) {
            $table->dropTimestamps();
        });
        Schema::table('orders_payment_status_log', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
}
