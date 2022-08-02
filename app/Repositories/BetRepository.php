<?php

namespace App\Repositories;

use App\Models\Bet;
use Illuminate\Support\Facades\Auth;

class BetRepository
{
    public function index(array $filters)
    {
        $bets = Bet::with(['user', 'phase'])->withCount(['bettors']);

        if (array_key_exists('search', $filters) && $filters['search'] !== '') {
            $bets->where(function ($query) use ($filters) {
                $query->orWhere('title', 'ilike', '%'.$filters['search'].'%')
                ->orWhere('stake', 'ilike', '%'.$filters['search'].'%')
                ->orWhere('description', 'ilike', '%'.$filters['search'].'%');
            });
        }

        if (array_key_exists('status', $filters) && $filters['status'] !== null) {
            $bets->where('status', $filters['status']);
        }

        if (array_key_exists('user_id', $filters) && $filters['user_id'] !== null) {
            $bets->where('user_id', $filters['user_id']);
        }

        if (! Auth::user()->isAdmin()) {
            $bets->where('user_id', Auth::user()->id);
        }

        return $bets->paginate();
    }

    public function store(array $data): Bet
    {
        $bet = new Bet();

        // default values ...
        $bet->user_id = Auth::user()->id;
        $bet->status = 'DRAFT';

        // ... erased by correct values if present
        $bet->fill($data);
        $bet->save();

        return $bet;
    }

    public function update(Bet $bet, array $data): Bet
    {
        $bet->fill($data);
        $bet->save();

        return $bet;
    }

    public function destroy(Bet $bet): void
    {
        $bet->delete();
    }

    public function modifyStatus(Bet $bet, string $newStatus): Bet
    {
        $bet->status = $newStatus;
        $bet->save();

        return $bet;
    }
}
