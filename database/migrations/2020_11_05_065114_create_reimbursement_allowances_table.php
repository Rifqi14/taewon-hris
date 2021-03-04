<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReimbursementAllowancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reimbursement_allowances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('reimbursement_id')->nullable();
            $table->unsignedBigInteger('driver_list_id')->nullable();
            $table->string('description')->nullable();
            $table->double('value')->nullable();
            $table->timestamps();

            $table->foreign('reimbursement_id')->references('id')->on('reimbursements')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reimbursement_allowances');
    }
}
