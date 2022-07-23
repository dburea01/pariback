<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompetitionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
        /// @todo : policies
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
            'short_name' => 'required|max:20|unique:competitions,short_name',
            'english_name' => 'required',
            'french_name' => 'required',
            'icon' => 'required|mimes:jpg,bmp,png|max:500',
            'position' => 'required|int|gt:0'
        ];
    }
}