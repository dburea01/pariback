<?php

namespace App\Repositories;

use App\Models\Sport;
use Spatie\QueryBuilder\QueryBuilder;

class SportRepository
{
    public function index()
    {
        return QueryBuilder::for(Sport::class)
        ->defaultSort('position')
        ->get();
    }

    public function store(array $data, string $imageName): Sport
    {
        $sport = new Sport();
        $sport->fill($data);
        $sport->name = [
            'en' => $data['english_name'],
            'fr' => $data['french_name'],
        ];
        $sport->status = 'INACTIVE';
        $sport->icon = $imageName;
        $sport->save();

        return $sport;
    }

    public function update(Sport $sport, array $data, string $imageName): Sport
    {
        $sport->icon = $imageName === '' ? null : $imageName;

        if (array_key_exists('french_name', $data)) {
            $sport->setTranslation('name', 'fr', $data['french_name']);
        }
        if (array_key_exists('english_name', $data)) {
            $sport->setTranslation('name', 'en', $data['english_name']);
        }
        $sport->save();

        return $sport;
    }

    public function destroy(Sport $sport): void
    {
        $sport->delete();
    }
}
