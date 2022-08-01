<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->bet);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'phase_id' => 'uuid|exists:phases,id',
            'points_good_score' => 'integer|gt:0',
            'points_good_1n2' => 'integer|gt:0',
            'status' => [
                'in:DRAFT,OPEN,CLOSED',
            ],
        ];
    }
}
