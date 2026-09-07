<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promocodes', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->integer('discount');
            $table->enum('discount_type', ['percentage', 'flat'])->default('percentage');
            $table->date('start');
            $table->date('end');
            $table->string('minimum_order_amount');
            $table->string('max_discount_amount');
            $table->string('message')->nullable();
            $table->timestamps();
            $table->enum('status', ['active', 'blocked'])->default('active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promocodes');
    }
};
