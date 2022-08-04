<?php
namespace App\Repositories;

use App\Models\Bet;
use App\Models\Bettor;
use App\Models\EventBetting;
use Illuminate\Support\Facades\DB;

class EventBettingRepository
{
    public function index(Bet $bet, Bettor $bettor)
    {
        $query = EventBetting::where('bettor_id', $bettor->id)->with(['event', 'bettor']);

        return $query->get()->sortBy('event.date');
    }

    public function store(Bettor $bettor, array $data): EventBetting
    {
        $this->destroyEventBettingWithBettorAndEvent($bettor->id, $data['event_id']);
        $eventBetting = new EventBetting();
        $eventBetting->fill($data);
        $eventBetting->bettor_id = $bettor->id;
        $eventBetting->save();

        return $eventBetting;
    }

    public function destroy(EventBetting $eventBetting): void
    {
        $eventBetting->delete();
    }

    public function destroyEventBettingWithBettorAndEvent(string $bettorId, string $eventId): void
    {
        EventBetting::where('bettor_id', $bettorId)->where('event_id', $eventId)->delete();
    }
}
