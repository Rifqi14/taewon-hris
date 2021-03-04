<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVehiclenameMaintanance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('maintanances', function (Blueprint $table) {
            $table->string('vehicle_name')->nullable();
            $table->string('vehicle_no')->nullable();
            $table->string('vehicle_category')->nullable();
            $table->string('vendor')->nullable();
            $table->string('technician')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('maintanances', function (Blueprint $table) {
            //
        });
    }
}
