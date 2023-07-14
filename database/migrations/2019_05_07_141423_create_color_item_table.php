<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColorItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('color_item', function (Blueprint $table) {
            $table->unsignedInteger('item_id')->nullable();
            $table->unsignedInteger('color_id')->nullable();
            $table->timestamps();
        });
        Schema::table('color_item', function (Blueprint $table) {
            $table->foreign('color_id')->references('id')
                ->on('colors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('color_item');
    }
}
