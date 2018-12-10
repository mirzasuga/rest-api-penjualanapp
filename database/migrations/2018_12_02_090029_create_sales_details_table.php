<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_details', function (Blueprint $table) {
            $table->unsignedInteger('sales_id')->index();
            $table->bigInteger('sell_price');
            $table->bigInteger('product_amount');
            $table->bigInteger('total_price');
            // $table->string('diskon');
            // $table->timestamps();
            $table->foreign('sales_id')->references('sales_id')->on('sales');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_details');
    }
}
