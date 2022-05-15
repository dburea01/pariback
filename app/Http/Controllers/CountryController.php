<?php
namespace App\Http\Controllers;

use App\Models\Country;
use App\Http\Requests\StoreCountryRequest;
use App\Http\Requests\UpdateCountryRequest;
use App\Http\Resources\CountryResource;
use App\Repositories\CountryRepository;
use phpDocumentor\Reflection\Types\Resource_;
use PhpParser\Node\Stmt\TryCatch;

class CountryController extends Controller
{
    private $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function index()
    {
        $countries = $this->countryRepository->index();

        return CountryResource::collection($countries);
    }

    public function show(Country $country)
    {
        return new CountryResource($country);
    }

    public function store(StoreCountryRequest $request)
    {
        try {
            $country = $this->countryRepository->store($request->all());
            return new CountryResource($country);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to create the country.']);
        }
    }

    public function update(UpdateCountryRequest $request, Country $country)
    {
        try {
            $countryUpdated = $this->countryRepository->update($country, $request->all());
            return new CountryResource($countryUpdated);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to update the country.']);
        }
    }

    public function destroy(Country $country)
    {
        $this->authorize('delete', $country);

        try {
            $this->countryRepository->destroy($country);
            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to delete the country.']);
        }
    }
}