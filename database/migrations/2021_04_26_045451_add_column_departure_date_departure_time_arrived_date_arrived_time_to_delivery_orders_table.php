<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDepartureDateDepartureTimeArrivedDateArrivedTimeToDeliveryOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->date('departure_date')->nullable();
            $table->time('departure_time')->nullable();
            $table->date('arrived_date')->nullable();
            $table->time('arrived_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropColumn('departure_date');
            $table->dropColumn('departure_time');
            $table->dropColumn('arrived_date');
            $table->dropColumn('arrived_time');
        });
    }
}
