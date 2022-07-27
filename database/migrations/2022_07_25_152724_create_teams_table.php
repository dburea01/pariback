<?php

use App\Models\Team;
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
        Schema::create('teams', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('country_id', 2)->nullable();
            $table->string('sport_id', 10)->nullable();
            $table->string('short_name');
            $table->string('name');
            $table->string('city');
            $table->string('icon');
            $table->string('status')->default('INACTIVE')->comment('ACTIVE / INACTIVE');
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('countries')->nullOnDelete();
            $table->foreign('sport_id')->references('id')->on('sports')->nullOnDelete();

            $table->unique(['country_id', 'sport_id', 'short_name']);
        });

        $teams = [
            [
                'country_id' => 'FR',
                'sport_id' => 'FOOT',
                'short_name' => 'PSG',
                'name' => 'Paris Saint Germain',
                'city' => 'Paris',
                'icon' => 'team_PSG.png',
                'status' => 'ACTIVE',
            ],
            [
                'country_id' => 'FR',
                'sport_id' => 'FOOT',
                'short_name' => 'LOSC',
                'name' => 'Lille Olympic Sporting Club',
                'city' => 'Lille',
                'icon' => 'team_LOSC.png',
                'status' => 'ACTIVE',
            ],
            [
                'country_id' => 'FR',
                'sport_id' => 'FOOT',
                'short_name' => 'OM',
                'name' => 'Olympic de Marseille',
                'city' => 'Marseille',
                'icon' => 'team_OM.png',
                'status' => 'ACTIVE',
            ],
            [
                'country_id' => 'FR',
                'sport_id' => 'FOOT',
                'short_name' => 'RCL',
                'name' => 'Racing Club de Lens',
                'city' => 'Lens',
                'icon' => 'team_RCL.png',
                'status' => 'ACTIVE',
            ],
        ];

        foreach ($teams as $team) {
            Team::create($team);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teams');
    }
};
