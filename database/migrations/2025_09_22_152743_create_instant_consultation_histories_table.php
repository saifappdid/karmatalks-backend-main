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
        Schema::create('instant_consultation_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('astro_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('connection_request_id')->nullable()->constrained('instant_connection_requests')->nullOnDelete();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('session_type')->comment('chat, voice, video');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->string('duration')->nullable();
            $table->integer('session_rate')->default(0);
            $table->string('remark')->nullable();
            $table->enum('status', ['ongoing', 'refunded', 'ended'])->default('ongoing');
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
        Schema::dropIfExists('instant_consultation_histories');
    }
};
