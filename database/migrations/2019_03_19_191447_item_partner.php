<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ItemPartner extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_partner', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('stand_id')->nullable();
            $table->unsignedInteger('item_id')->nullable();
            $table->unsignedTinyInteger('status')->nullable()->default(1);
            $table->index(['item_id']);
            $table->index(['stand_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_partner');
    }
}
