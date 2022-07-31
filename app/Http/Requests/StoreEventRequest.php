<?php

namespace App\Http\Requests;

use App\Models\Event;
use App\Models\Participation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Str;

class StoreEventRequest extends FormRequest
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
            'team1_id' => 'required|uuid',
            'team2_id' => 'required|uuid|different:team1_id',
            'date' => 'required|date_format:Y-m-d H:i',
            'status' => 'required|in:PLANNED,INPROGRESS,TERMINATED',
            'score1' => 'nullable|integer|min:0',
            'score2' => 'nullable|integer|min:0',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // the teams must belong to the competition of the phase received
            if ($this->team1_id && Str::isUuid($this->team1_id) && $this->getParticipationTeamPhase($this->team1_id, $this->phase->id) === 0) {
                $validator->errors()->add('team1_id', trans('validation_others.team_not_belong_competition', ['team' => 'team1']));
            }

            if ($this->team2_id && Str::isUuid($this->team2_id) && $this->getParticipationTeamPhase($this->team2_id, $this->phase->id) === 0) {
                $validator->errors()->add('team2_id', trans('validation_others.team_not_belong_competition', ['team' => 'team2']));
            }

            // the team cannot belong to several events of the same phase

            if ($this->team1_id && Str::isUuid($this->team1_id) && $this->getEventsTeamPhase($this->team1_id, $this->phase->id) !== 0) {
                $validator->errors()->add('team1_id', trans('validation_others.team_already_present_for_the_phase', ['team' => 'team1']));
            }

            if ($this->team2_id && Str::isUuid($this->team2_id) && $this->getEventsTeamPhase($this->team2_id, $this->phase->id) !== 0) {
                $validator->errors()->add('team2_id', trans('validation_others.team_already_present_for_the_phase', ['team' => 'team2']));
            }
        });
    }

    public function getParticipationTeamPhase(string $teamId, string $phaseId): int
    {
        $participation = Participation::join('competitions', 'competitions.id', 'participations.competition_id')
        ->join('phases', 'phases.competition_id', 'competitions.id')
                ->where('participations.team_id', $teamId)
                ->where('phases.id', $phaseId)
                ->get();

        return $participation->count();
    }

    public function getEventsTeamPhase(string $teamId, string $phaseId): int
    {
        $event = Event::where('phase_id', $phaseId)
        ->where(function ($query) use ($teamId) {
            $query->where('team1_id', $teamId)
            ->orWhere('team2_id', $teamId);
        })
        ->get();

        return $event->count();
    }
}
