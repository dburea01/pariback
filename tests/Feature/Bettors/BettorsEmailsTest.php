<?php

namespace Tests\Feature;

use App\Models\Bet;
use App\Models\Bettor;
use App\Models\Phase;
use App\Models\User;
use App\Models\UserHistoEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BettorsEmailsTest extends TestCase
{
    use RefreshDatabase;
    use Request;
    use InsertData;

    public function test_the_activation_of_bet_generate_email_invitation(): void
    {
        Notification::fake();

        $this->insert_data();
        $phase = Phase::first();
        $user = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $bet = Bet::factory()->create(['user_id' => $user->id, 'phase_id' => $phase->id, 'status' => 'DRAFT']);
        $this->create_bettors($bet, 3);

        $this->actingAs($user);
        $response = $this->patchJson($this->getEndPoint()."bets/$bet->id/activate");
        $response->assertStatus(200);
        Notification::assertCount(3);
    }

    public function test_the_resend_of_the_mail_invitation(): void
    {
        Notification::fake();

        $this->insert_data();
        $phase = Phase::first();
        $user = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $bet = Bet::factory()->create(['user_id' => $user->id, 'phase_id' => $phase->id, 'status' => 'INPROGRESS']);
        $this->create_bettors($bet, 3);
        $bettor = Bettor::where('bet_id', $bet->id)->first();

        $this->actingAs($user);
        $response = $this->postJson($this->getEndPoint()."bets/$bet->id/bettors/$bettor->id/resend-email-invitation");
        $response->assertStatus(200);
        Notification::assertCount(1);
    }

    public function test_the_resend_of_the_mail_invitation_only_for_bet_inprogress(): void
    {
        Notification::fake();

        $this->insert_data();
        $phase = Phase::first();
        $user = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $bet = Bet::factory()->create(['user_id' => $user->id, 'phase_id' => $phase->id, 'status' => 'DRAFT']);
        $this->create_bettors($bet, 3);
        $bettor = Bettor::where('bet_id', $bet->id)->first();

        $this->actingAs($user);
        $response = $this->postJson($this->getEndPoint()."bets/$bet->id/bettors/$bettor->id/resend-email-invitation");
        $response->assertStatus(403);
        Notification::assertCount(0);
    }

    public function test_the_resend_of_the_mail_invitation_is_limited_for_a_day(): void
    {
        Notification::fake();

        $this->insert_data();
        $phase = Phase::first();
        $user = User::factory()->create(['is_admin' => false, 'status' => 'VALIDATED']);
        $bet = Bet::factory()->create(['user_id' => $user->id, 'phase_id' => $phase->id, 'status' => 'INPROGRESS']);
        $this->create_bettors($bet, 2);
        $bettorToResend = Bettor::where('bet_id', $bet->id)->first();

        UserHistoEmail::factory()->count(config('params.max_emails_resent_email_invitation_a_day'))->create([
            'user_id' => $bettorToResend->user_id,
            'email_type' => 'INVITATION_SENT',
            'sent_at' => now(),
        ]);

        $this->actingAs($user);
        $response = $this->postJson($this->getEndPoint()."bets/$bet->id/bettors/$bettorToResend->id/resend-email-invitation");
        $response->assertStatus(422);
        Notification::assertCount(0);
    }

    public function create_bettors(Bet $bet, int $quantityBettors)
    {
        $users = User::factory()->count($quantityBettors)->create();
        foreach ($users as $user) {
            Bettor::factory()->create(['bet_id' => $bet->id, 'user_id' => $user->id]);
        }
    }
}
