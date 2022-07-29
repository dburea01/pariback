<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('phase_id');
            $table->uuid('team1_id');
            $table->uuid('team2_id');
            $table->dateTime('date');
            $table->string('location')->nullable();
            $table->string('status')->comment('PLANNED - INPROGRESS - TERMINATED');
            $table->tinyInteger('score_team1')->nullable();
            $table->tinyInteger('score_team2')->nullable();
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('phase_id')->references('id')->on('phases')->cascadeOnDelete();
            $table->foreign('team1_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->foreign('team2_id')->references('id')->on('teams')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
};
