<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinanceTransactionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finance_transaction_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->enum('category', [1, 2])->comment('[1]: Plus, [2]: Minus');
            $table->boolean('is_pending')->comment('[0]: hanya dapat diakses di transaksi, [1]: Hanya dapat diakses di pending');
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
        Schema::dropIfExists('finance_transaction_types');
    }
}
