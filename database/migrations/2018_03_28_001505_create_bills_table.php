<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->integer('signupId')->unsigned();
			$table->primary('signupId');
            $table->foreign('signupId')->references('id')->on('signups')->onDelete('cascade');
            
            $table->decimal('amount', 8, 2);

            $table->integer('serial')->unsigned()->nullable();
            $table->string('code')->nullable();  //虛擬帳號
            $table->string('sevenCodes')->nullable();  // 超商條碼
            $table->date('deadLine')->nullable();

            $table->dateTime('payDate')->nullable();
            $table->boolean('payed');
            $table->integer('paywayId')->unsigned()->nullable();
			

			$table->integer('updatedBy')->unsigned()->nullable();
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
        Schema::dropIfExists('bills');
    }
}
