<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTodoManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('todo_managers', function (Blueprint $table) {
            $table->unsignedInteger('todo_type_id');
            $table->unsignedInteger('todo_status_id');
            $table->unsignedInteger('todo_state_id');
            $table->unsignedInteger('todo_priority_id');
            $table->unsignedInteger('responsible_id');
            $table->unsignedInteger('author_id');
            $table->unsignedInteger('order_id')->nullable();
            $table->timestamp('date')->nullable();
            $table->text('comment')->nullable();
            $table->increments('id');
            $table->timestamps();

            $table->foreign('todo_type_id')->references('id')->on('todo_types');
            $table->foreign('todo_status_id')->references('id')->on('todo_statuses');
            $table->foreign('todo_state_id')->references('id')->on('todo_states');
            $table->foreign('todo_priority_id')->references('id')->on('todo_priorities');
            $table->foreign('responsible_id')->references('user_id')->on('user');
            $table->foreign('author_id')->references('user_id')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('todo_managers');
    }
}
