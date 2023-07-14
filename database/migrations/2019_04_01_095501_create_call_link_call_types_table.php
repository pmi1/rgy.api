<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCallLinkCallTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('call_link_call_types', function (Blueprint $table) {
            $table->unsignedInteger('call_type_id');
            $table->string('call_entry_id', 50)->unique();
            $table->increments('id');
            $table->timestamps();
        });

        Schema::table('call_link_call_types', function (Blueprint $table){
           $table->foreign('call_type_id')
               ->references('id')
               ->on('call_types')
               ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('call_link_call_types');
    }
}
