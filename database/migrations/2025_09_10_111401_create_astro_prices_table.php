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
        Schema::create('astro_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('astro_id')->constrained('astros')->onDelete('cascade');
            $table->string('duration')->nullable()->comment('in minutes');
            $table->string('questions')->nullable()->comment('number of questions');
            $table->integer('charge')->nullable();
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
        Schema::dropIfExists('astro_prices');
    }
};
