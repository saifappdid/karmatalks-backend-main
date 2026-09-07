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
        Schema::create('schedule_consultation_call_histories', function (Blueprint $table) {
            $table->id();
            $table->string('initiated_by')->comment('user, astro');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('astro_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel_id')->nullable();
            $table->timestamp('call_start_time')->nullable();
            $table->timestamp('call_end_time')->nullable();
            $table->string('status')->default('initiated')->comment('initiated, ringing, ongoing, completed, failed, busy, no-answer, canceled');
            $table->string('remark')->nullable();
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
        Schema::dropIfExists('schedule_consultation_call_histories');
    }
};
