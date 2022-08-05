<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBet extends Model
{
    use HasFactory;
    use HasUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'bet_id',
        'user_id',
        'event_id',
        'score_team1',
        'score_team2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bet()
    {
        return $this->belongsTo(Bet::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function team1()
    {
        return $this->hasOneThrough(Team::class, Event::class, 'id', 'id', 'event_id', 'team1_id');
    }

    public function team2()
    {
        return $this->hasOneThrough(Team::class, Event::class, 'id', 'id', 'event_id', 'team2_id');
    }

    public function phase()
    {
        return $this->hasOneThrough(Phase::class, Event::class, 'id', 'id', 'event_id', 'phase_id');
    }
}
