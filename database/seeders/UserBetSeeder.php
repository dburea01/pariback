<?php

namespace Database\Seeders;

use App\Models\Bet;
use App\Models\Bettor;
use App\Models\Event;
use App\Models\UserBet;
use Illuminate\Database\Seeder;

class UserBetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bets = Bet::all();

        foreach ($bets as $bet) {
            $events = Event::where('phase_id', $bet->phase_id)->get();
            $bettors = Bettor::where('bet_id', $bet->id)->get();

            foreach ($events as $event) {
                foreach ($bettors as $bettor) {
                    UserBet::factory()->create([
                        'bet_id' => $bet->id,
                        'user_id' => $bettor->user_id,
                        'event_id' => $event->id,
                    ]);
                }
            }
        }
    }
}
