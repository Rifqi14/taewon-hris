<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->date('join_date')->nullable();
            $table->date('resign_date')->nullable();
            $table->string('bpjs_tenaga_kerja')->nullable();
            $table->string('ptkp')->nullable();
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
            $table->dropColumn('join_date');
            $table->dropColumn('resign_date');
            $table->dropColumn('bpjs_tenaga_kerja');
            $table->dropColumn('ptkp');
        });
    }
}
