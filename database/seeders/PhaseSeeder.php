<?php

namespace Database\Seeders;

use App\Models\Competition;
use App\Models\Phase;
use Illuminate\Database\Seeder;

class PhaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $competitions = Competition::all();

        foreach ($competitions as $competition) {
            Phase::factory()->count(rand(5, 10))->create([
                'competition_id' => $competition->id,
            ]);
        }
    }
}
