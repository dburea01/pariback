<?php
namespace App\Models;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    use HasUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'phase_id',
        'team1_id',
        'team2_id',
        'date',
        'location',
        'status',
        'score_team1',
        'score_team2',
    ];

    protected function getDateAttribute($value)
    {
        $date = new DateTime($value);

        return $date->format('Y-m-d H:i');
    }

    public function phase()
    {
        return $this->belongsTo(Phase::class);
    }

    public function team1()
    {
        return $this->belongsTo(Team::class, 'team1_id');
    }

    public function team2()
    {
        return $this->belongsTo(Team::class, 'team2_id');
    }

    public function userBets()
    {
        return $this->hasMany(UserBet::class);
    }

    public function getStartedAttribute()
    {
        return Carbon::now()->addMinutes(15) > Carbon::createFromFormat('Y-m-d H:i', $this->date) ? true : false;
    }
}
