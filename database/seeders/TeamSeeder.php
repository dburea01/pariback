<?php
namespace Database\Seeders;

use App\Models\Country;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = Country::all();
        $sports = Sport::all();

        foreach ($countries as $country) {
            foreach ($sports as $sport) {
                try {
                    Team::factory()->count(25)->create([
                        'country_id' => $country->id,
                        'sport_id' => $sport->id
                    ]);
                } catch (\Throwable $th) {
                    echo 'doublon, skip';
                }
            }
        }
    }
}
