<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventBettingRequest extends FormRequest
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
            'score_team1' => 'required|integer|gte:0',
            'score_team2' => 'required|integer|gte:0',
            'event_id' => 'required|uuid|exists:events,id',
        ];

        // @todo : verifier cohérence entre bet_id, bettor_id, event_id
    }
}
