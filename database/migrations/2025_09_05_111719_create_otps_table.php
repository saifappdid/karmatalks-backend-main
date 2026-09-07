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
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 200)->nullable();
            $table->enum('type', ['user', 'astro'])->nullable();
            $table->string('otp', 200)->nullable();
            $table->string('expired_at', 200)->nullable();
            $table->timestamps();
            $table->enum('status', ['active', 'expired'])->default('active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('otps');
    }
};
