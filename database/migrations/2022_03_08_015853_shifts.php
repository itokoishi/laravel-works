<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->integer('staff_id')->default(0);
            $table->date('date');
            $table->string('start_h', 255);
            $table->string('start_m', 255);
            $table->string('end_h', 255);
            $table->string('end_m', 255);
            $table->dateTime('created_at');
            $table->dateTime('updated_at');
            $table->boolean('cancel_flag')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedules');
    }
};
