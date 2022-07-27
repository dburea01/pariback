<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Competition extends Model
{
    use HasFactory;
    use HasTranslations;
    use HasUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'country_id',
        'sport_id',
        'short_name',
        'name',
        'icon',
        'status',
        'position',
        'start_date',
        'end_date'
    ];

    public $translatable = [
        'name',
    ];

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
