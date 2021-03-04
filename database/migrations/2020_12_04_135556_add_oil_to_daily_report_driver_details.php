<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOilToDailyReportDriverDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_report_driver_details', function (Blueprint $table) {
            $table->float('oil')->nullable();
            $table->renameColumn('police', 'etc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_report_driver_details', function (Blueprint $table) {
            $table->dropIfExists('oil');
            $table->renameColumn('etc', 'police');
        });
    }
}