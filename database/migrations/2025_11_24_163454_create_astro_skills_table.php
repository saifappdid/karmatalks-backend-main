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
        Schema::create('astro_skills', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->enum('status', ['active', 'blocked'])->default('active');
            $table->foreignId('astro_id')->nullable()->constrained('astros')->nullOnDelete();
            $table->foreignId('skill_id')->nullable()->constrained('skills')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('astro_skills');
    }
};
