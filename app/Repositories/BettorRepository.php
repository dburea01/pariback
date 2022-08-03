<?php
namespace App\Repositories;

use App\Models\Bet;
use App\Models\Bettor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BettorRepository
{
    public function index(Bet $bet)
    {
        return DB::table('bettors')
        ->join('users', 'users.id', 'bettors.user_id')
        ->where('bettors.bet_id', $bet->id)
        ->orderBy('users.name')
        ->select('bettors.id', 'users.id as user_id', 'users.name', 'users.email', 'users.status', 'bettors.token', 'bettors.invitation_sent_at')
        ->get();
    }

    public function getBettorsOfBet(Bet $bet)
    {
        return Bettor::with('user')->where('bet_id', $bet->id)->get()->sortBy('user.name');
    }

    public function store(array $data): Bettor
    {
        $bettor = new Bettor();
        $bettor->fill($data);
        $bettor->token = Str::random(6);
        $bettor->save();

        return $bettor;
    }

    public function destroy(Bettor $bettor): void
    {
        $bettor->delete();
    }
}
