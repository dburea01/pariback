<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
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
            'date' => 'date_format:Y-m-d H:i',
            'status' => 'in:PLANNED,INPROGRESS,TERMINATED',
            'score1' => 'nullable|integer|min:0',
            'score2' => 'nullable|integer|min:0',
        ];
    }
}
