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
        Schema::table('staffs', function (Blueprint $table) {
            $table->string('image', 255)->after('name_kana');
            $table->string('birth_year', 255)->after('image');
            $table->string('birth_month', 255)->after('birth_year');
            $table->string('birth_date', 255)->after('birth_month');
            $table->boolean('view_flag', 255)->after('birth_date');
            $table->dropColumn('age');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
