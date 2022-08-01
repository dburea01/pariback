<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePhaseRequest extends FormRequest
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
        if ($this->method() === 'POST') {
            return [
                'short_name' => [
                    'required',
                    'min:2',
                    Rule::unique('phases')
                    ->where(fn ($query) => $query->where('competition_id', $this->competition->id)),
                ],
                'english_name' => 'required|min:5',
                'french_name' => 'required|min:5',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d|after:start_date',
                'status' => 'required|in:ACTIVE,INACTIVE',
            ];
        }

        if ($this->method() === 'PUT') {
            return [
                'short_name' => [
                    'min:2',
                    Rule::unique('phases')
                    ->where(fn ($query) => $query->where('competition_id', $this->competition->id))
                    ->ignore($this->phase),
                ],
                'english_name' => 'min:5',
                'french_name' => 'min:5',
                'start_date' => 'date_format:Y-m-d',
                'end_date' => 'date_format:Y-m-d|after:start_date',
                'status' => 'in:ACTIVE,INACTIVE',
            ];
        }
    }

    public function withValidator($validator)
    {
        // the dates of the phase must be included in the phase of the competition
        $validator->after(function ($validator) {
            if (
                $this->start_date < $this->competition->start_date
                ||
                $this->start_date > $this->competition->end_date
                ||
                $this->end_date < $this->competition->start_date
                ||
                $this->end_date > $this->competition->end_date
            ) {
                // todo : make an internationalizable message
                $validator->errors()->add('dates', 'The dates are not included in the dates of the competition : '.$this->competition->start_date.' ==> '.$this->competition->end_date);
            }
        });
    }
}
