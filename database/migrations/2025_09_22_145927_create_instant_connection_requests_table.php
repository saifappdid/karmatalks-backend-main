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
        Schema::create('instant_connection_requests', function (Blueprint $table) {
            $table->id();
            $table->string('type')->comment('chat, voice, video');
            $table->string('initiated_by')->comment('user, astro');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('astro_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel_id')->nullable();
            $table->string('status')->default('initiated')->comment('initiated, ringing, ongoing, completed, failed, busy, no-answer, canceled');
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
        Schema::dropIfExists('instant_connection_requests');
    }
};
