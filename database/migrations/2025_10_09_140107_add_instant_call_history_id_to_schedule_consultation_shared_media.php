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
        Schema::table('schedule_consultation_shared_media', function (Blueprint $table) {
            $table->foreignId('instant_call_id')->nullable()->constrained('instant_consultation_histories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedule_consultation_shared_media', function (Blueprint $table) {
            //
        });
    }
};
