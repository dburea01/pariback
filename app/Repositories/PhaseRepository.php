<?php

namespace App\Repositories;

use App\Models\Competition;
use App\Models\Phase;

class PhaseRepository
{
    public function index(string $competitionId)
    {
        return Phase::where('competition_id', $competitionId)->orderBy('start_date')->get();
    }

    public function store(Competition $competition, array $data): Phase
    {
        $phase = new Phase();
        $phase->competition_id = $competition->id;
        $phase->fill($data);
        $phase->name = [
            'en' => $data['english_name'],
            'fr' => $data['french_name'],
        ];
        $phase->save();

        return $phase;
    }

    public function update(Phase $phase, array $data): Phase
    {
        $phase->fill($data);

        if (array_key_exists('french_name', $data)) {
            $phase->setTranslation('name', 'fr', $data['french_name']);
        }
        if (array_key_exists('english_name', $data)) {
            $phase->setTranslation('name', 'en', $data['english_name']);
        }

        $phase->save();

        return $phase;
    }

    public function destroy(Phase $phase): void
    {
        $phase->delete();
    }
}
