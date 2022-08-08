<?php

namespace App\Services;

use App\Models\Bet;
use App\Models\Bettor;
use App\Models\Event;
use App\Models\User;
use App\Models\UserBet;
use App\Repositories\UserBetRepository;
use App\Repositories\UserHistoEmailRepository;

class BetService
{
    public $userBetRepository;

    public $userHistoEmailRepository;

    public function __construct(
        UserHistoEmailRepository $userHistoEmailRepository,
        UserBetRepository $userBetRepository
    ) {
        $this->userHistoEmailRepository = $userHistoEmailRepository;
        $this->userBetRepository = $userBetRepository;
    }

    public function bettorsWithRankOld(Bet $bet)
    {
        $bettors = Bettor::where('bet_id', $bet->id)->with(['user', 'bet'])->get();

        foreach ($bettors as $bettor) {
            // find the events of the bet + userBets ot the bettor
            // $userBetsOfBettor = $this->userBetRepository->index($bet, ['userId' => $bettor->user_id]);
            $userBetsOfBettor = UserBet::where('bet_id', $bet->id)
            ->where('user_id', $bettor->user_id)
            ->with('event')
            ->get();

            $bettor->quantity_points_bet = $this->calculatePoints($bet, $userBetsOfBettor);
            $bettor->results_bettor = $userBetsOfBettor;
        }

        return $bettors->sortByDesc('quantity_points');
    }

    public function bettorsWithRank(Bet $bet)
    {
        $events = Event::where('phase_id', $bet->phase_id)->with(['team1', 'team2'])
        ->orderBy('date')
        ->select('events.id', 'events.phase_id', 'events.team1_id', 'events.team2_id', 'events.date', 'events.status', 'events.score_team1', 'events.score_team2')
        ->get();

        $users = User::join('bettors', 'bettors.user_id', 'users.id')
        ->where('bettors.bet_id', $bet->id)
        ->select('users.id', 'users.name')
        ->get();

        foreach ($events as $keyEvent => $event) {
            $resultUsers = [];
            foreach ($users as $user) {
                $userBetOfBettor = UserBet::where('bet_id', $bet->id)
                ->where('user_id', $user->id)
                ->where('event_id', $event->id)
                ->first();

                $resultUsers[] = [
                    'user' => $user,
                    'score_team1' => $userBetOfBettor ? $userBetOfBettor->score_team1 : null,
                    'score_team2' => $userBetOfBettor ? $userBetOfBettor->score_team2 : null,
                    'quantity_points' => $userBetOfBettor ? $this->calculatePoints($bet, $userBetOfBettor, $event) : null,
                ];

                $events[$keyEvent]->bettors = $resultUsers;
            }
        }

        return $events;
    }

    public function calculatePoints(Bet $bet, UserBet $userBet, Event $event): int
    {
        $quantityPointsUserBet = 0;
        if (! is_null($userBet->score_team1) && ! is_null($userBet->score_team2)) {
            /**
             * good score
             */
            if ($userBet->score_team1 === $event->score_team1
                    &&
                    $userBet->score_team2 === $event->score_team2) {
                $quantityPointsUserBet = $bet->points_good_score;

                /**
                 * good 1N2
                 */
            } elseif (
                ($userBet->score_team1 > $userBet->score_team2 &&
                $event->score_team1 > $event->score_team2)
                ||
                ($userBet->score_team1 < $userBet->score_team2 &&
                $event->score_team1 < $event->score_team2)
                ||
                ($userBet->score_team1 == $userBet->score_team2 &&
                $event->score_team1 == $event->score_team2)

            ) {
                $quantityPointsUserBet = $bet->points_good_1n2;
            }
        }

        return $quantityPointsUserBet;
    }

    public function calculatePointsOld(Bet $bet, $userBets): int
    {
        $quantityPointsBet = 0;

        foreach ($userBets as $userBet) {
            // calculate the points if the match is done

            $quantityPointsUserBet = 0;
            if (! is_null($userBet->event->score_team1) && ! is_null($userBet->event->score_team2)) {
                /**
                 * good score
                 */
                if ($userBet->event->score_team1 === $userBet->score_team1
                    &&
                    $userBet->event->score_team2 === $userBet->score_team2) {
                    $quantityPointsUserBet = $bet->points_good_score;

                    /**
                     * good 1N2
                     */
                } elseif (
                    ($userBet->event->score_team1 > $userBet->event->score_team2 &&
                    $userBet->score_team1 > $userBet->score_team2)
                    ||
                    ($userBet->event->score_team1 < $userBet->event->score_team2 &&
                    $userBet->score_team1 < $userBet->score_team2)
                    ||
                    ($userBet->event->score_team1 == $userBet->event->score_team2 &&
                    $userBet->score_team1 == $userBet->score_team2)

                ) {
                    $quantityPointsUserBet = $bet->points_good_1n2;
                }

                $userBet->points_for_this_user_bet = $quantityPointsUserBet;
                $quantityPointsBet = $quantityPointsBet + $quantityPointsUserBet;
            }
        }

        return $quantityPointsBet;
    }
}
