<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LogisticZoneUpdateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logistic_zones', function (Blueprint $table) {
            $table->bigInteger('cost_per_kg')->nullable();
            $table->bigInteger('additional_cost')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logistic_zones', function (Blueprint $table) {
            $table->dropColumn('cost_per_kg');
            $table->dropColumn('additional_cost');
        });
    }
}
