<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActiveNonActiveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warning_letters', function (Blueprint $table) {
            $table->integer('sp_active')->nullable();
            $table->integer('sp_non_active')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warning_letters', function (Blueprint $table) {
            $table->dropColumn('sp_active');
            $table->dropColumn('sp_non_active');
        });
    }
}
