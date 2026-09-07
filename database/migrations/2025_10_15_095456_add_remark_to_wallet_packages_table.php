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
        Schema::table('wallet_packages', function (Blueprint $table) {
            $table->text('remark')->nullable();
            $table->string('for_new_user')->default('no')->comment('yes,no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wallet_packages', function (Blueprint $table) {
            //
        });
    }
};
