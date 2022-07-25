<?php

namespace App\Repositories;

use App\Models\Country;
use Spatie\QueryBuilder\QueryBuilder;

class CountryRepository
{
    public function index()
    {
        return QueryBuilder::for(Country::class)
        ->defaultSort('position')
        ->get();
    }

    public function store(array $data, string $icon): Country
    {
        $country = new Country();
        $country->fill($data);
        $country->name = [
            'en' => $data['english_name'],
            'fr' => $data['french_name'],
        ];
        $country->icon = $icon;
        $country->status = 'INACTIVE';
        $country->save();

        return $country;
    }

    public function update(Country $country, array $data, string $icon): Country
    {
        $country->fill($data);
        $country->icon = $icon;
        if (array_key_exists('french_name', $data)) {
            $country->setTranslation('name', 'fr', $data['french_name']);
        }
        if (array_key_exists('english_name', $data)) {
            $country->setTranslation('name', 'en', $data['english_name']);
        }
        $country->save();

        return $country;
    }

    public function destroy(Country $country): void
    {
        $country->delete();
    }
}
