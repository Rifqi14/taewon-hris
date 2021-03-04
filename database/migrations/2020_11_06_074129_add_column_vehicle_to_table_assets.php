<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnVehicleToTableAssets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('asset_type')->nullable();
            $table->string('license_no')->nullable();
            $table->string('engine_no')->nullable();
            $table->string('merk')->nullable();
            $table->string('type')->nullable();
            $table->string('model')->nullable();
            $table->integer('production_year')->nullable();
            $table->string('manufacture')->nullable();
            $table->string('engine_capacity')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            
        });
    }
}
