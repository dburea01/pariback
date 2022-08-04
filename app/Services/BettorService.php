<?php

namespace App\Services;

use App\Models\Bet;
use App\Models\Bettor;
use App\Models\User;
use App\Notifications\SendEmailInvitationBet;
use App\Repositories\EventBettingRepository;
use App\Repositories\UserHistoEmailRepository;
use Illuminate\Support\Facades\Log;

class BettorService
{
    public $eventBettingRepository;

    public $userHistoEmailRepository;

    public function __construct(
        // EventBettingRepository $eventBettingRepository,
        UserHistoEmailRepository $userHistoEmailRepository
    ) {
        // $this->eventBettingRepository = $eventBettingRepository;
        $this->userHistoEmailRepository = $userHistoEmailRepository;
    }

    public function bettorsWithRank(Bet $bet)
    {
        $bettors = Bettor::with('user')->where('bet_id', $bet->id)->get();

        foreach ($bettors as $bettor) {
            // find the events of the bet + eventBettings ot the bettor
            $eventBettingsForThisBettor = $this->eventBettingRepository->index($bet, $bettor);

            $bettor->quantity_points = $this->calculatePoints($bet, $eventBettingsForThisBettor);
            $bettor->results_bettor = $eventBettingsForThisBettor;
        }

        return $bettors->sortByDesc('quantity_points');
    }

    public function calculatePoints(Bet $bet, $eventBettings): int
    {
        $quantityPoints = 0;

        foreach ($eventBettings as $eventBetting) {
            // calculate the points if the match is done
            if (! is_null($eventBetting->event_score_team1) && ! is_null($eventBetting->event_score_team2)) {
                /**
                 * good score
                 */
                if ($eventBetting->event_score_team1 === $eventBetting->event_bettings_score_team1
                    &&
                    $eventBetting->event_score_team2 === $eventBetting->event_bettings_score_team2) {
                    $quantityPoints = $quantityPoints + $bet->points_good_score;

                    /**
                     * good 1N2
                     */
                } elseif (
                    ($eventBetting->event_score_team1 > $eventBetting->event_score_team2 &&
                    $eventBetting->event_bettings_score_team1 > $eventBetting->event_bettings_score_team2)
                    ||
                    ($eventBetting->event_score_team1 < $eventBetting->event_score_team2 &&
                    $eventBetting->event_bettings_score_team1 < $eventBetting->event_bettings_score_team2)
                    ||
                    ($eventBetting->event_score_team1 == $eventBetting->event_score_team2 &&
                    $eventBetting->event_bettings_score_team1 == $eventBetting->event_bettings_score_team2)

                ) {
                    $quantityPoints = $quantityPoints + $bet->points_good_1n2;
                }
            }
        }

        return $quantityPoints;
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
                Log::info('invitation pour user '.$bettor->user_id);
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
