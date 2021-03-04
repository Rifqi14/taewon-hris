<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutsourcingAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outsourcing_addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('outsourcing_id')->nullable();
            $table->unsignedBigInteger('province_id')->index();
            $table->unsignedBigInteger('region_id')->index();
            $table->unsignedBigInteger('district_id')->index();
            $table->text('address');
            $table->string('kode_pos');
            $table->boolean('default');
            $table->timestamps();
            $table->foreign('outsourcing_id')->references('id')->on('outsourcings')
                  ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('province_id')->references('id')->on('provinces')
                  ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('region_id')->references('id')->on('regions')
                  ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('district_id')->references('id')->on('districts')
                  ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outsourcing_addresses');
    }
}
