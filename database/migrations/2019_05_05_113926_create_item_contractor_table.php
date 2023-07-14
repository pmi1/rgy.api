<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemContractorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('item_contractor', 'item_contractor_'.time());
        Schema::create('item_contractor', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('item_id')->nullable();
            $table->unsignedInteger('contractor_id')->nullable();
            $table->double('price')->nullable();
            $table->unsignedInteger('min_quantity')->nullable();
            $table->unsignedInteger('available_quantity')->nullable();
            $table->unsignedInteger('state')->nullable();
            $table->unsignedTinyInteger('approved')->nullable()->default(1);
            $table->unsignedTinyInteger('status')->nullable()->default(1);
            $table->index(['item_id']);
            $table->index(['contractor_id']);
            $table->unique(['item_id', 'contractor_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_contractor');
    }
}
