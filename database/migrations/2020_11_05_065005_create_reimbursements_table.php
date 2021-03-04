<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReimbursementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reimbursements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date')->nullable();
            $table->string('notes')->nullable();
            $table->unsignedBigInteger('daily_report_driver_id');
            $table->unsignedBigInteger('driver_id');
            $table->time('max_arrival')->nullable();
            $table->date('get_day')->nullable();
            $table->double('subtotal')->nullable();
            $table->double('subtotalallowance')->nullable();
            $table->double('grandtotal')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reimbursements');
    }
}
