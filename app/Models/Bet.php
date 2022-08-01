<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    use HasFactory;
    use HasUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'phase_id',
        'title',
        'description',
        'stake',
        'status',
        'points_good_score',
        'points_good_1n2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bettors()
    {
        return $this->hasMany(Bettor::class);
    }

    public function phase()
    {
        return $this->belongsTo(Phase::class);
    }
}
