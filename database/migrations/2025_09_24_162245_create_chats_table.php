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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->nullable()->constrained('instant_consultation_histories')->nullOnDelete();
            $table->string('message')->nullable();
            $table->string('file')->nullable();
            $table->string('sender_type')->comment('astro or user')->nullable();
            $table->bigInteger('sender_id')->nullable()->comment('reference to astro or user');
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
        Schema::dropIfExists('chats');
    }
};
