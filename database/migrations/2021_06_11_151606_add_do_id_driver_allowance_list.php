<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDoIdDriverAllowanceList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_allowance_lists', function (Blueprint $table) {
            $table->unsignedBigInteger('delivery_order_id')->nullable();
            $table->foreign('delivery_order_id')->references('id')->on('driver_allowance_lists')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_allowance_lists', function (Blueprint $table) {
            $table->dropForeign(['delivery_oreder_id']);
            $table->dropColumn('delivery_oreder_id');
        });
    }
}
