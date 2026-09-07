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
        Schema::table('astros', function (Blueprint $table) {
            $table->integer('instant_voice_call_charge_dollar')->nullable();
            $table->integer('instant_video_call_charge_dollar')->nullable();
            $table->integer('instant_chat_charge_dollar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('astros', function (Blueprint $table) {
            //
        });
    }
};
