<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetMovementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_movements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('asset_serial_id')->nullable();
            $table->string('production_no')->nullable();
            $table->string('ref_no')->nullable();
            $table->date('transaction_date')->nullable();
            $table->date('expired_date')->nullable();
            $table->string('type')->nullable();
            $table->integer('qty')->nullable();
            $table->timestamps();

            $table->foreign('asset_serial_id')->references('id')->on('asset_serials')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asset_movements');
    }
}
