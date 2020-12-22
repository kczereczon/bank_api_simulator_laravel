<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('transaction_id')->unsigned();
            $table->string('nrb_ben');
            $table->string('name_ben');
            $table->string('address_ben')->nullable();
            $table->float('amount');
            $table->bigInteger('prin_banking_account_id')->unsigned()->nullable();
            $table->bigInteger('ben_banking_account_id')->unsigned()->nullable();
            $table->date('posting_date');
            $table->string('nrb_prin');
            $table->string('name_prin');
            $table->string('address_prin')->nullable();
            $table->timestamps();

            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('prin_banking_account_id')->references('id')->on('banking_accounts')->onDelete('cascade');
            $table->foreign('ben_banking_account_id')->references('id')->on('banking_accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operations');
    }
}
