<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('nrb_ben');
            $table->string('name_ben');
            $table->string('address_ben')->nullable();
            $table->float('amount');
            $table->string('title');
            $table->bigInteger('prin_client_id')->unsigned()->nullable();
            $table->bigInteger('ben_client_id')->unsigned()->nullable();
            $table->string('nrb_prin');
            $table->string('name_prin');
            $table->bigInteger('status_id')->unsigned();
            $table->string('direction');
            $table->date('realisation_date');
            $table->timestamps();

            $table->foreign('ben_client_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('status')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
