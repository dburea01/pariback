<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class StoreBettorRequest extends FormRequest
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
            'name' => 'required',
            'email' => 'required|email',
        ];
    }

    // the user must be unique for this bet
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = DB::table('users')
            ->join('bettors', 'users.id', 'bettors.user_id')
            ->where('bettors.bet_id', $this->route('bet')->id)
            ->where('users.email', $this->email)
            ->first();

            if ($user) {
                $validator->errors()->add('email', trans('validation_others.user_already_bettor', ['email' => $this->email]));
            }
        });
    }
}
