<?php

use App\Models\Competition;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competitions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('country_id', 2);
            $table->string('sport_id', 10);
            $table->string('short_name', 20);
            $table->string('name');
            $table->string('icon');
            $table->tinyInteger('position');
            $table->string('status')->default('ACTIVE')->comment('ACTIVE / INACTIVE');
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('countries')->cascadeOnDelete();
            $table->foreign('sport_id')->references('id')->on('sports')->cascadeOnDelete();
        });

        $competitions = [
            [
                'country_id' => 'FR',
                'sport_id' => 'FOOT',
                'short_name' => 'FRANCE-FOOT-L1',
                'name' => [
                    'en' => 'French football championship - league 1',
                    'fr' => 'Championnat de France de football - Ligue 1'
                ],
                'icon' => 'competition_L1',
                'status' => 'ACTIVE',
                'position' => 10
            ],
            [
                'country_id' => 'FR',
                'sport_id' => 'FOOT',
                'short_name' => 'FRANCE-FOOT-L2',
                'name' => [
                    'en' => 'French football championship - league 2',
                    'fr' => 'Championnat de France de football - Ligue 2'
                ],
                'icon' => 'competition_L2',
                'status' => 'ACTIVE',
                'position' => 20
            ],
        ];

        foreach ($competitions as $competition) {
            Competition::create($competition);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('competitions');
    }
};
