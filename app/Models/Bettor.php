<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bettor extends Model
{
    use HasFactory;
    use HasUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'bet_id',
        'user_id',
        'token',
    ];

    public function bet()
    {
        return $this->belongsTo(Bet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function eventBettings()
    {
        return $this->hasMany(EventBetting::class);
    }
}
