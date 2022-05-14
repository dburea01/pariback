<?php

declare(strict_types=1);
namespace App\Repositories;

use App\Models\PasswordReset;
use Illuminate\Support\Str;

class PasswordResetRepository
{
    public function insert(string $email) : PasswordReset
    {
        $passwordReset = new PasswordReset();
        $passwordReset->email = $email;
        $passwordReset->token = Str::random(20);
        $passwordReset->created_at = now();
        $passwordReset->save();

        return $passwordReset;
    }

    public function destroy(string $email): void
    {
        PasswordReset::where('email', $email)->delete();
    }
}
