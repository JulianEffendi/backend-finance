<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinanceTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_transaction')->unique();
            $table->double('amount', 11, 2)->nullable();
            $table->text('file')->nullable();
            $table->dateTime('date')->nullable();
            $table->boolean('is_active')->default(0)->comment('[0]: Not-active, [1]: Active / Sumable');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('type_id');
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users');

            $table->foreign('type_id')
                  ->references('id')
                  ->on('finance_transaction_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('finance_transactions');
    }
}