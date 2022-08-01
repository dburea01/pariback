<?php
namespace Database\Seeders;

use App\Models\Bet;
use App\Models\Bettor;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BettorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bets = Bet::all();
        $users = User::where('status', 'VALIDATED')->get();

        foreach ($bets as $bet) {
            $bettors = $users->random(random_int(1, count($users)));

            foreach ($bettors as $bettor) {
                Bettor::factory()->create([
                    'bet_id' => $bet->id,
                    'user_id' => $bettor->id,
                ]);
            }
        }
    }
}
