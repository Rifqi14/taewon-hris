<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSiteIdCodeSystemToGroupAllowancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_allowances', function (Blueprint $table) {
            $table->integer('site_id')->nullable();
            $table->string('code_system')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_allowances', function (Blueprint $table) {
            $table->dropColumn('site_id');
            $table->dropColumn('code_system');
        });
    }
}
