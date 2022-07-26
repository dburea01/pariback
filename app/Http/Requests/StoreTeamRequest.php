<?php
namespace App\Http\Requests;

use App\Models\Team;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'country_id' => 'required|exists:countries,id',
            'sport_id' => 'required|exists:sports,id',
            'short_name' => 'required',
            'name' => 'required',
            'city' => 'required',
            'status' => 'in:ACTIVE,INACTIVE',
            'icon' => [
                'required',
                'mimes:jpg,bmp,png',
                'max:500',
                Rule::dimensions()->maxWidth(100)->maxHeight(100),
            ],
        ];
    }

    // the short name must be unique for the sport and country
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $quantityTeam = Team::where('country_id', $this->country_id)
            ->where('sport_id', $this->sport_id)
            ->where('short_name', strtoupper($this->short_name))
            ->count();

            if ($quantityTeam > 0) {
                $validator->errors()->add('team', 'The short name already exists for this country and sport');
            }
        });
    }
}
