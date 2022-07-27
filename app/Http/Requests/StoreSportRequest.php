<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id' => 'required|max:10|unique:sports,id',
            'english_name' => 'required',
            'french_name' => 'required',
            'position' => 'required|int|gt:0',
            'icon' => [
                'required',
                'mimes:jpg,bmp,png',
                'max:500',
                Rule::dimensions()->maxWidth(100)->maxHeight(100),
            ],
        ];
    }
}
