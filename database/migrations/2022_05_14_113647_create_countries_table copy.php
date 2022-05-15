<?php

use App\Models\Country;
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
        Schema::create('countries', function (Blueprint $table) {
            $table->string('id', 2)->primary();
            $table->string('local_name');
            $table->string('english_name');
            $table->string('icon')->nullable();
            $table->string('status')->default('ACTIVE')->comment('ACTIVE / INACTIVE');
            $table->tinyInteger('position');
            $table->timestamps();
        });

        $countries = [
            [
                'id' => 'FR',
                'local_name' => 'France',
                'english_name' => 'France',
                'icon' => 'flag_fr',
                'status' => 'ACTIVE',
                'position' => 10
            ],
            [
                'id' => 'IT',
                'local_name' => 'Italia',
                'english_name' => 'Italy',
                'icon' => 'flag_it',
                'status' => 'ACTIVE',
                'position' => 20
            ],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
};
