<?php
namespace App\Policies;

use App\Models\Bet;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BetPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    public function view(User $user, Bet $bet)
    {
        return $user->id === $bet->user_id;
    }

    public function update(User $user, Bet $bet)
    {
        return $user->id === $bet->user_id;
    }

    public function delete(User $user, Bet $bet)
    {
        return $user->id === $bet->user_id;
    }

    public function activate(User $user, Bet $bet)
    {
        return $user->id === $bet->user_id;
    }
}
