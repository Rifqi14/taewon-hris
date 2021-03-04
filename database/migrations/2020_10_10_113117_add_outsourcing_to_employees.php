<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOutsourcingToEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('outsourcing_id');
        });
        
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('outsourcing_id')->nullable();
            $table->foreign('outsourcing_id')->references('id')->on('outsourcings')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('outsourcing_id')->nullable();
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign('outsourcing_id');
            $table->dropColumn('outsourcing_id');
        });
    }
}