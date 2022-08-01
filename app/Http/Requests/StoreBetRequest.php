<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreBetRequest extends FormRequest
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
        // TODO : add a control about the quantity of bets for 1 user ?

        return [
            'user_id' => [
                Rule::requiredIf(Auth::user()->is_admin),
                'uuid',
                Rule::exists('users', 'id')->where(function ($query) {
                    return $query->where('status', 'VALIDATED');
                }),
            ],
            'phase_id' => 'required|uuid|exists:phases,id',
            'title' => 'required',
            'points_good_score' => 'required|integer|gt:0',
            'points_good_1n2' => 'required|integer|gt:0',
            'status' => [
                Rule::requiredIf(Auth::user()->is_admin),
                'in:DRAFT,OPEN,CLOSED',
            ],
        ];
    }
}
