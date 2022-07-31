<?php

namespace App\Repositories;

use App\Models\Event;
use App\Models\Phase;

class EventRepository
{
    public function index(Phase $phase)
    {
        return Event::where('phase_id', $phase->id)->with(['team1', 'team2'])->orderBy('date')->get();
    }

    public function index2(array $filters)
    {
        $events = Event::join('phases', 'phases.id', 'events.phase_id')
        ->join('competitions', 'competitions.id', 'phases.competition_id')
        ->join('teams as team1', 'team1.id', 'events.team1_id')
        ->join('teams as team2', 'team2.id', 'events.team2_id')
        ->join('countries', 'competitions.country_id', 'countries.id')
        ->join('sports', 'competitions.sport_id', 'sports.id')
        ->select(
            'countries.icon as country_icon',
            'sports.icon as sport_icon',
            'competitions.id as competition_id',
            'competitions.name as competition_name',
            'competitions.short_name as competition_short_name',
            'competitions.icon as competition_icon',
            'phases.id as phase_id',
            'phases.short_name as phase_short_name',
            'events.id',
            'events.date as event_date',
            'events.location',
            'events.status',
            'events.score_team1',
            'events.score_team2',
            'team1.id as team1_id',
            'team1.short_name as team1_short_name',
            'team1.name as team1_name',
            'team1.icon as team1_icon',
            'team2.id as team2_id',
            'team2.short_name as team2_short_name',
            'team2.name as team2_name',
            'team2.icon as team2_icon'
        )
        ->orderBy('events.date');

        if (array_key_exists('country_id', $filters) && $filters['country_id'] !== null) {
            $events->where('competitions.country_id', $filters['country_id']);
        }

        if (array_key_exists('sport_id', $filters) && $filters['sport_id'] !== null) {
            $events->where('competitions.sport_id', $filters['sport_id']);
        }

        if (array_key_exists('status', $filters) && $filters['status'] !== null) {
            $events->where('events.status', $filters['status']);
        }

        if (array_key_exists('phase_id', $filters) && $filters['phase_id'] !== null) {
            $events->where('phase_id', $filters['phase_id']);
        }

        return $events->paginate();
    }

    public function store(Phase $phase, array $data): Event
    {
        $event = new Event();
        $event->phase_id = $phase->id;
        $event->fill($data);
        $event->save();

        return $event;
    }

    public function update(Event $event, array $data): Event
    {
        $event->fill($data);
        $event->save();

        return $event;
    }

    public function destroy(Event $event): void
    {
        $event->delete();
    }
}
