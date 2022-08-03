<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreBettorRequest;
use App\Http\Resources\BettorResource;
use App\Models\Bet;
use App\Models\Bettor;
use App\Models\User;
use App\Repositories\BettorRepository;
use App\Repositories\UserHistoEmailRepository;
use App\Repositories\UserRepository;
use App\Services\BettorService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BettorController extends Controller
{
    private $bettorRepository;

    private $userRepository;
    private $userHistoEmailRepository;
    private $bettorService;

    public function __construct(
        BettorRepository $bettorRepository,
        UserRepository $userRepository,
        UserHistoEmailRepository $userHistoEmailRepository,
        BettorService $bettorService,
    ) {
        $this->bettorRepository = $bettorRepository;
        $this->userRepository = $userRepository;
        $this->userHistoEmailRepository = $userHistoEmailRepository;
        $this->bettorService = $bettorService;
    }

    public function index(Bet $bet)
    {
        $bettors = $this->bettorRepository->getBettorsOfBet($bet);

        return BettorResource::collection($bettors);
    }

    public function store(Bet $bet, StoreBettorRequest $request)
    {
        // see the form validation for the authorizations
        $user = User::where('email', $request->email)->first();

        try {
            if (!$user) {
                $user = $this->userRepository->insert([
                    'name' => $request->name,
                    'email' => $request->email,
                    'status' => 'CREATED',
                ]);
            }

            $bettor = $this->bettorRepository->store([
                'bet_id' => $bet->id,
                'user_id' => $user->id,
            ]);

            return new BettorResource($bettor);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to create the bettor.' . $th->getMessage()]);
        }
    }

    public function destroy(Bet $bet, Bettor $bettor)
    {
        $this->authorize('delete', [Bettor::class, $bet]);
        try {
            $this->bettorRepository->destroy($bettor);

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Impossible to delete the bettor.' . $th->getMessage()]);
        }
    }

    public function resendEmailInvitation(Bet $bet, Bettor $bettor)
    {
        // TODO : authorizations

        abort_if($bet->id !== $bettor->bet_id, 404);
        abort_if($bet->status !== 'INPROGRESS', 403, trans('messages.bet_not_activated'));
        $user = User::find($bettor->user_id);

        DB::beginTransaction();
        try {
            $quantityEmailSent = $this->userHistoEmailRepository->userHistoEmailOfTheDay($user->id, 'INVITATION_SENT');
            if ($quantityEmailSent < config('params.max_emails_resent_email_invitation_a_day')) {
                $organizer = User::find($bet->user_id);
                $this->bettorService->sendInvitationOneBettor($bettor, $organizer, $bet);
                DB::commit();

                return response()->json([
                    'success' => trans(
                        'messages.email_resent_successfully',
                        ['name' => $user->name],
                        422
                    )]);
            } else {
                Log::info('[RESEND_EMAIL_INVITATION] Max email sent for INVITATION_SENT for today for email ' . $user->email);

                return response()->json([
                    'error' => trans(
                        'messages.max_emails_resent_email_invitation_a_day',
                        ['name' => $user->name, 'qtyMax' => config('params.max_emails_resent_email_invitation_a_day')],
                        422
                    )]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => trans(
                    'messages.email_invitation_not_resent_successfully',
                    ['name' => $user->name, 'msgError' => $th->getMessage()],
                    422
                )]);
        }
    }
}
