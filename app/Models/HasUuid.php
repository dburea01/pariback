<?php
namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

trait HasUuid
{
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            $model->id = Uuid::uuid4()->toString();
            $model->created_by = Auth::check() ? Auth::user()->name : '?';
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::check() ? Auth::user()->name : '?';
        });
    }
}
