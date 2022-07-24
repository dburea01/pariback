<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreCountryRequest;
use App\Http\Requests\UpdateCountryRequest;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use App\Repositories\CountryRepository;
use App\Services\ImageService;

class CountryController extends Controller
{
    private $countryRepository;

    private $imageService;

    public function __construct(CountryRepository $countryRepository, ImageService $imageService)
    {
        $this->countryRepository = $countryRepository;
        $this->imageService = $imageService;
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
            $imageName = $this->imageName($request->id, $request->icon);
            $this->imageService->uploadImage($imageName, $request->icon);
            $country = $this->countryRepository->store($request->all(), $imageName);

            return new CountryResource($country);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to create the country.' . $th->getMessage()]);
        }
    }

    public function update(UpdateCountryRequest $request, Country $country)
    {
        try {
            if ($request->has('icon')) {
                $imageName = $this->imageName($country->id, $request->icon);
                $this->imageService->deleteImage($country->icon);
                $this->imageService->uploadImage($imageName, $request->icon);
            } else {
                $imageName = $country->icon;
            }

            $countryUpdated = $this->countryRepository->update($country, $request->all(), $imageName);

            return new CountryResource($countryUpdated);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to update the country.' . $th->getMessage()]);
        }
    }

    public function destroy(Country $country)
    {
        $this->authorize('delete', $country);

        try {
            $this->imageService->deleteImage($country->icon);
            $this->countryRepository->destroy($country);

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to delete the country.' . $th->getMessage()]);
        }
    }

    public function imageName(string $countryId, $image)
    {
        return 'country_' . strtoupper($countryId) . '.' . $image->getClientOriginalExtension();
    }
}
