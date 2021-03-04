<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToWorkgroupAllowances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workgroup_allowances', function (Blueprint $table) {
            $table->string('value')->nullable();
            $table->string('type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workgroup_allowances', function (Blueprint $table) {
            $table->dropColumn('value');
            $table->dropColumn('type');
        });
    }
}
