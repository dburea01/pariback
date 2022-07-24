<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreCountryRequest extends FormRequest
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
            'id' => [
                // TODO :add regex validation for 2 characters , something like [A-Z]
                'required', 'max:2', 'unique:countries,id',
            ],
            'local_name' => 'required',
            'english_name' => 'required',
            'position' => 'required|int|gt:0',
            'icon' => [
                'required',
                'mimes:jpg,bmp,png',
                'max:500',
                Rule::dimensions()->maxWidth(100)->maxHeight(100)->ratio(1),
            ]
        ];
    }
}
