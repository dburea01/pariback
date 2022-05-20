<?php
namespace App\Repositories;

use App\Models\Competition;
use App\Models\Sport;
use Illuminate\Support\Facades\Auth;

class CompetitionRepository
{
    public function index()
    {
        $competitions = Competition::with(['country', 'sport'])->orderBy('position');

        if (
            !Auth::check()
            ||
            (Auth::check() && !Auth::user()->isAdmin())
        ) {
            $competitions->where('status', 'ACTIVE');
        }
        return $competitions->get();
    }

    public function store(array $data, string $imageName): Competition
    {
        $competition = new Competition();
        $competition->fill($data);
        $competition->name = [
            'en' => $data['english_name'],
            'fr' => $data['french_name']
        ];
        $competition->status = 'INACTIVE';
        // $competition->icon = $imageName;
        $competition->save();

        return $competition;
    }

    public function update(Competition $competition, array $data, string $imageName): Competition
    {
        $competition->icon = $imageName === '' ? null : $imageName;

        if (array_key_exists('french_name', $data)) {
            $competition->setTranslation('name', 'fr', $data['french_name']);
        }
        if (array_key_exists('english_name', $data)) {
            $competition->setTranslation('name', 'en', $data['english_name']);
        }
        $competition->save();

        return $competition;
    }

    public function destroy(Competition $competition): void
    {
        $competition->delete();
    }
}
