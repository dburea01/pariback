<?php
namespace App\Http\Requests;

use App\Models\Competition;
use App\Models\Team;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreParticipationRequest extends FormRequest
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
            'competition_id' => 'required|uuid|exists:competitions,id',
            'team_id' => [
                'required',
                'uuid',
                'exists:teams,id',
                Rule::unique('participations')->where(function ($query) {
                    $query->where('competition_id', $this->competition_id);
                }),
            ],
        ];
    }

    // the team must have the same sport than the competition

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $team = Team::find($this->team_id);
            $competition = Competition::find($this->competition_id);

            if ($team->sport_id <> $competition->sport_id) {
                $validator->errors()->add('team_id', 'The team and competition must have the same sport.');
            }
        });
    }
}
