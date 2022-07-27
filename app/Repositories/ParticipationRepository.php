<?php
namespace App\Repositories;

use App\Models\Participation;

class ParticipationRepository
{
    public function index(array $filters)
    {
        $participations = Participation::join('teams', 'teams.id', 'participations.team_id')
            ->join('competitions', 'competitions.id', 'participations.competition_id')
            ->orderBy('teams.city');

        if (array_key_exists('team_id', $filters) && $filters['team_id'] !== null) {
            $participations->where('team_id', $filters['team_id']);
        }

        if (array_key_exists('competition_id', $filters) && $filters['competition_id'] !== null) {
            $participations->where('competition_id', $filters['competition_id']);
        }

        return $participations->paginate();
    }

    public function store(array $request): Participation
    {
        $participation = new Participation();
        $participation->competition_id = $request['competition_id'];
        $participation->team_id = $request['team_id'];
        $participation->save();

        return $participation;
    }

    public function destroy(Participation $participation): void
    {
        $participation->delete();
    }
}
