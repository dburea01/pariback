<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Phase extends Model
{
    use HasFactory;
    use HasTranslations;
    use HasUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'competition_id',
        'short_name',
        'name',
        'start_date',
        'end_date',
        'status',
    ];

    public $translatable = [
        'name',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
