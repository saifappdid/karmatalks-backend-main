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
        Schema::create('astros', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->text('profile')->nullable();
            $table->enum('gender', ['male', 'female', 'other']);
            $table->date('dob')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->unique()->nullable();
            $table->text('address')->nullable();
            $table->string('known_languages')->nullable();
            $table->string('experience')->nullable();
            $table->json('skills')->nullable();
            $table->text('biography')->nullable();
            $table->string('password')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->enum('is_online', ['0', '1'])->default('0');
            $table->integer('instant_voice_call_charge')->nullable();
            $table->integer('instant_video_call_charge')->nullable();
            $table->integer('instant_chat_charge')->nullable();
            $table->enum('online_for_voice_call', ['yes', 'no'])->default('no');
            $table->enum('online_for_video_call', ['yes', 'no'])->default('no');
            $table->enum('online_for_chat', ['yes', 'no'])->default('no');
            $table->string('available_for_schedule_consultation')->nullable()->comment('voice,video,chat');
            $table->text('description')->nullable();
            $table->string('device_id')->nullable();
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
        Schema::dropIfExists('astros');
    }
};
