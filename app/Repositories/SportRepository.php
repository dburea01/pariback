<?php
namespace App\Repositories;

use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Sport;
use Spatie\QueryBuilder\AllowedSort;
use StringLengthSort;

class SportRepository
{
    public function index()
    {
        $sports = QueryBuilder::for(Sport::class)
        ->defaultSort('position')
        ->get();

        return $sports;
    }

    public function store(array $data): Sport
    {
        $sport = new Sport();
        $sport->fill($data);
        $sport->name = [
            'en' => $data['english_name'],
            'fr' => $data['french_name']
        ];
        $sport->status = 'INACTIVE';
        $sport->save();

        return $sport;
    }

    public function update(Sport $sport, array $data): Sport
    {
        $sport->fill($data);

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
