<?php
namespace App\Http\Requests;

use App\Models\Team;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateTeamRequest extends FormRequest
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
            'country_id' => 'exists:countries,id',
            'sport_id' => 'exists:sports,id',
            'status' => 'in:ACTIVE,INACTIVE',
            'icon' => [
                'mimes:jpg,bmp,png',
                'max:500',
                Rule::dimensions()->maxWidth(100)->maxHeight(100),
            ],
        ];
    }
}
