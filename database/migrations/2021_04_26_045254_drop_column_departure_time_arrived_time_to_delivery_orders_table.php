<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnDepartureTimeArrivedTimeToDeliveryOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_orders', function (Blueprint $table) {
            $table->dropColumn('departure_time');
            $table->dropColumn('arrived_time');
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
            //
        });
    }
}
