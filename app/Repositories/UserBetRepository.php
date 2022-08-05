<?php
namespace App\Repositories;

use App\Models\Bet;
use App\Models\Bettor;
use App\Models\EventBetting;
use App\Models\UserBet;
use Illuminate\Support\Str;

class UserBetRepository
{
    public function index(Bet $bet, array $filters)
    {
        $query = UserBet::where('bet_id', $bet->id)->with(['event', 'user']);

        if (array_key_exists('userId', $filters) && Str::isUuid($filters['userId'])) {
            $query->where('user_id', $filters['userId']);
        }

        if (array_key_exists('eventId', $filters) && Str::isUuid($filters['eventId'])) {
            $query->where('event_id', $filters['eventId']);
        }

        return $query->get()->sortBy('event.date');
    }

    public function store(Bet $bet, array $request): UserBet
    {
        $this->destroyUserBetWithUserAndEvent($bet->id, $request['user_id'], $request['event_id']);
        $userBet = new UserBet();
        $userBet->fill($request);
        $userBet->bet_id = $bet->id;
        $userBet->save();

        return $userBet;
    }

    public function destroy(UserBet $userBet): void
    {
        $userBet->delete();
    }

    public function destroyUserBetWithUserAndEvent(string $betId, string $userId, string $eventId): void
    {
        UserBet::where('bet_id', $betId)
        ->where('event_id', $eventId)
        ->where('user_id', $userId)
        ->delete();
    }
}
