<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemEquipmentMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_equipment_materials', function (Blueprint $table) {
            $table->unsignedInteger('item_id')->nullable();
            $table->unsignedInteger('equipment_material_id')->nullable();
            $table->timestamps();
        });

            Schema::table('item_equipment_materials', function (Blueprint $table) {
                $table->foreign('equipment_material_id')->references('id')
                    ->on('equipment_materials')->onDelete('cascade');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_equipment_materials');
    }
}
