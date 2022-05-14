<?php
namespace App\Repositories;

use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Country;

class CountryRepository
{
    public function index()
    {
        $countries = QueryBuilder::for(Country::class)
        ->allowedSorts('local_name', 'english_name', 'position')
        ->defaultSort('position')
        ->get();

        return $countries;
    }

    public function store(array $data): Country
    {
        $country = new Country();
        $country->fill($data);
        $country->status = 'INACTIVE';
        $country->save();

        return $country;
    }

    public function update(Country $country, array $data): Country
    {
        $country->fill($data);
        $country->save();

        return $country;
    }

    public function destroy(Country $country): void
    {
        $country->delete();
    }
}
