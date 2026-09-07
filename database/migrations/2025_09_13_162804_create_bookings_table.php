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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('astro_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->nullable()->comment('voice, video');
            $table->string('name')->nullable();
            $table->string('mobile')->nullable();
            $table->date('session_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('duration')->nullable();
            $table->string('questions')->nullable();
            $table->string('promocode')->nullable();
            $table->double('promocode_discount')->nullable();
            $table->double('wallet')->nullable();
            $table->double('sub_total')->nullable();
            $table->double('final_total')->nullable();
            $table->string('booking_uid')->nullable();
            $table->json('booking_data')->nullable();
            $table->enum('status', ['confirmed', 'started', 'ended'])->default('confirmed');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
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
        Schema::dropIfExists('bookings');
    }
};
