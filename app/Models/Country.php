<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    public $table = 'countries';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'local_name',
        'english_name',
        'icon',
        'status',
        'position'
    ];
}
