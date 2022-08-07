<?php
namespace App\Services;

use App\Models\Bet;
use App\Models\Bettor;
use App\Models\User;
use App\Notifications\SendEmailInvitationBet;
use App\Repositories\UserHistoEmailRepository;
use Illuminate\Support\Facades\Log;

class BettorService
{
    public $eventBettingRepository;

    public $userHistoEmailRepository;

    public function __construct(
        UserHistoEmailRepository $userHistoEmailRepository
    ) {
        $this->userHistoEmailRepository = $userHistoEmailRepository;
    }

    public function sendInvitationBettors(Bet $bet)
    {
        $bettors = Bettor::where('bet_id', $bet->id)->get();
        $organizer = User::find($bet->user_id);

        foreach ($bettors as $bettor) {
            if (is_null($bettor->invitation_sent_at)) {
                $this->sendInvitationOneBettor($bettor, $organizer, $bet);
            }

            if (config('app.env') !== 'production') {
                Log::info('invitation pour user ' . $bettor->user_id);
                Log::info('attente 2 sec poru mailtrap.....');
                sleep(2);
            }
        }
    }

    public function sendInvitationOneBettor(Bettor $bettor, User $organizer, Bet $bet)
    {
        $user = User::find($bettor->user_id);
        $user->notify(new SendEmailInvitationBet($bettor->token, $organizer, $bet));
        $bettor->invitation_sent_at = now();
        $bettor->save();
        $this->userHistoEmailRepository->insert($user->id, 'INVITATION_SENT');
    }
}
