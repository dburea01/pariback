<?php
namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventBetting;
use App\Models\Phase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventBettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bettors = DB::table('bets')
        ->join('bettors', 'bettors.bet_id', 'bets.id')
        ->where('bets.status', 'INPROGRESS')
        ->select('bettors.id', 'bets.phase_id')
        ->get();

        $phases = Phase::where('status', 'ACTIVE')->get();

        foreach ($bettors as $bettor) {
            $phasesFiltered = $phases->filter(function ($phase) use ($bettor) {
                return $phase->id === $bettor->phase_id;
            });

            foreach ($phasesFiltered as $phaseFiltered) {
                $events = Event::where('phase_id', $phaseFiltered->id)->get();

                foreach ($events as $event) {
                    EventBetting::factory()->create([
                        'bettor_id' => $bettor->id,
                        'event_id' => $event->id,
                    ]);
                }
            }
        }
    }
}
