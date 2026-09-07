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
        Schema::create('schedule_consultation_shared_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('call_history_id')->nullable()->constrained('schedule_consultation_call_histories')->nullOnDelete();
            $table->string('shared_by')->comment('user, astro');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('astro_id')->nullable()->constrained()->nullOnDelete();
            $table->string('media');
            $table->enum('status', ['active', 'blocked'])->default('active');
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
        Schema::dropIfExists('schedule_consultation_shared_media');
    }
};
