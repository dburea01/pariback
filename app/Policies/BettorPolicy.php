<?php

namespace App\Policies;

use App\Models\Bet;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BettorPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    public function create(User $user, Bet $bet)
    {
        return $bet->user_id === $user->id;
    }

    public function delete(User $user, Bet $bet)
    {
        return $bet->user_id === $user->id;
    }
}
