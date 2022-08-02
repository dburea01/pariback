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
        Schema::create('bettors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('bet_id');
            $table->uuid('user_id');
            $table->string('token')->unique();
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->timestamp('invitation_sent_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('bet_id')->references('id')->on('bets')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bettors');
    }
};
