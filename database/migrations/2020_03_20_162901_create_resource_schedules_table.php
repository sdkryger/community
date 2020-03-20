<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResourceSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resource_schedules', function (Blueprint $table) {
            $table->id();
            $table->integer('resource_id');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('user_id');
            $table->boolean('approved')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('resource_schedules');
    }
}
