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
        Schema::create('astro_working_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('astro_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('astro_working_day_id')->nullable()->constrained()->cascadeOnDelete();
            $table->time('start_time');
            $table->time('end_time');
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
        Schema::dropIfExists('astro_working_hours');
    }
};
