<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryToDriverAllowancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('driver_allowances', function (Blueprint $table) {
            $table->dropForeign(['allowance_id']);
            $table->dropColumn('allowance_id');
            $table->string('category')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_allowances', function (Blueprint $table) {
            $table->foreign('allowance_id')->references('id')->on('allowances')->onUpdate('cascade');
            $table->unsignedBigInteger('allowance_id')->nullable();
            $table->dropColumn('category');
        });
    }
}