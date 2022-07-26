<?php
namespace App\Repositories;

use App\Models\Team;
use Illuminate\Support\Facades\Auth;

class TeamRepository
{
    public function index(array $filters)
    {
        $teams = Team::with(['country', 'sport']);

        if (array_key_exists('name', $filters) && $filters['name'] !== '') {
            $teams->where(function ($query) use ($filters) {
                $query->orWhere('name', 'ilike', '%' . $filters['name'] . '%')
                ->orWhere('short_name', 'ilike', '%' . $filters['name'] . '%')
                ->orWhere('city', 'ilike', '%' . $filters['name'] . '%');
            });
        }

        if (array_key_exists('country_id', $filters) && $filters['country_id'] !== null) {
            $teams->where('country_id', $filters['country_id']);
        }

        if (array_key_exists('sport_id', $filters) && $filters['sport_id'] !== null) {
            $teams->where('sport_id', $filters['sport_id']);
        }

        if (array_key_exists('status', $filters) && $filters['status'] !== null) {
            $teams->where('status', $filters['status']);
        }

        return $teams->paginate();
    }

    public function store(array $data, string $icon): Team
    {
        $team = new Team();
        $team->fill($data);
        $team->icon = $icon;
        $team->save();

        return $team;
    }

    public function update(Team $team, array $data, string $imageName): Team
    {
        $team->fill($data);
        $team->icon = $imageName === '' ? null : $imageName;
        $team->save();

        return $team;
    }

    public function destroy(Team $team): void
    {
        $team->delete();
    }
}
