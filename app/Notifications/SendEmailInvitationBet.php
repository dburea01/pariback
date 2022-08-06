<?php

namespace App\Notifications;

use App\Models\Bet;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class SendEmailInvitationBet extends Notification
{
    /* to submit the send via a queue : uncomment this
    use Queueable;
    */

    public $token;

    public $organizer;

    public $bet;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $token, User $organizer, Bet $bet)
    {
        $this->token = $token;
        $this->organizer = $organizer;
        $this->bet = $bet;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = config('app.url').'/tobet/'.$this->token;

        $message = new MailMessage();
        $message->subject(trans('emailInvitationBet.subject', ['app_name' => config('app.name')]));
        $message->greeting(trans('emailInvitationBet.hello', ['name' => $notifiable->name]));

        $message->line(new HtmlString(trans(
            'emailInvitationBet.p1',
            [
                'organizer_name' => $this->organizer->name,
                'app_name' => config('app.name'),
            ]
        )));

        $message->line(new HtmlString(trans('emailInvitationBet.p2', ['bet_title' => $this->bet->title])));
        $message->line(new HtmlString(trans('emailInvitationBet.p3', ['bet_stake' => $this->bet->stake])));
        $message->action(trans('emailInvitationBet.button_i_bet'), $url)->level('success');

        $message->line(new HtmlString(trans('emailInvitationBet.personal_link')));

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
        ];
    }
}
