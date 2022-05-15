<?php

use App\Models\Sport;
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
        Schema::create('sports', function (Blueprint $table) {
            $table->string('id', 10)->primary();
            $table->json('name');
            $table->string('icon')->nullable();
            $table->string('status')->default('ACTIVE')->comment('ACTIVE / INACTIVE');
            $table->tinyInteger('position');
            $table->timestamps();
        });

        $sports = [
            [
                'id' => 'FOOT',
                'name' => [
                    'en' => 'Football',
                    'fr' => 'Football in french'
                ],
                'icon' => 'icon_foot',
                'status' => 'ACTIVE',
                'position' => 10
            ],
            [
                'id' => 'RUGBY',
                'name' => [
                    'en' => 'Rugby',
                    'fr' => 'Rugby in french'
                ],
                'icon' => 'icon_rugby',
                'status' => 'ACTIVE',
                'position' => 20
            ],
        ];

        foreach ($sports as $sport) {
            Sport::create($sport);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sports');
    }
};
