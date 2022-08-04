<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_bettings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('bettor_id');
            $table->uuid('event_id');
            $table->tinyInteger('score_team1')->nullable();
            $table->tinyInteger('score_team2')->nullable();
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('bettor_id')->references('id')->on('bettors')->cascadeOnDelete();
            $table->foreign('event_id')->references('id')->on('events')->cascadeOnDelete();
            $table->unique(['bettor_id', 'event_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_bettings');
    }
};
