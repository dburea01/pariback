<?php
namespace App\Http\Requests;

use App\Models\Competition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompetitionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
        // @todo : policies
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
            'short_name' => [
                'required',
                'max:20',
                Rule::unique('competitions')->ignore($this->competition->id)
            ],
            'english_name' => 'required',
            'french_name' => 'required',
            'icon' => 'required|mimes:jpg,bmp,png|max:500',
            'position' => 'required|int|gt:0'
        ];
    }
}