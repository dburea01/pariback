<?php

namespace Database\Seeders;

use App\Models\Bet;
use App\Models\Phase;
use App\Models\User;
use Illuminate\Database\Seeder;

class BetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::where('status', 'VALIDATED')->get();
        $phases = Phase::where('status', 'ACTIVE')->get();

        foreach ($users as $user) {
            for ($i = 0; $i < random_int(1, 5); $i++) {
                Bet::factory()->create([
                    'user_id' => $user->id,
                    'phase_id' => $phases->random()->id,
                ]);
            }
        }
    }
}
