<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreUserBetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (null !== $this->route('token') && isset($this->user_id)) {
            Auth::login(User::find($this->user_id));
        }

        return $this->user()->can('create', [UserBet::class, $this->bet]);
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
            'user_id' => [
                'required',
                'uuid',
                Rule::exists('bettors')->where(function ($query) {
                    return $query->where('user_id', $this->user_id)
                    ->where('bet_id', $this->route('bet')->id);
                }),
            ],
            'event_id' => 'required|uuid',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // check integrity between event and bet
            if (Str::isUuid($this->event_id)) {
                $exists = DB::table('events')
                ->join('bets', 'bets.phase_id', 'events.phase_id')
                ->where('bets.id', $this->route('bet')->id)
                //todo: impossible to bet for an event which is STARTED
                ->where('events.id', $this->event_id)
                ->get();

                if (count($exists) === 0) {
                    $validator->errors()->add('event_id', trans('validation_others.event_not accepted_for_this_bet'));
                }
            }
        });
    }
}
