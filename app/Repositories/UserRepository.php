<?php

declare(strict_types=1);
namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    /**
     * [Description for insert]
     *
     * @param array<string> $data
     *
     * @return User
     *
     */
    public function insert(array $data): User
    {
        $user = new User();
        $user->fill($data);
        $user->save();

        return $user;
    }

    /**
     * [Description for validateRegistration]
     *
     * @param User $user
     *
     * @return User
     *
     */
    public function validateRegistration(User $user): User
    {
        $user->update([
            'status' => 'VALIDATED',
            'email_verified_at' => now()
        ]);

        return $user;
    }

    public function modifyPassword(string $email, string $password): void
    {
        User::where('email', $email)
        ->update([
            'password' => Hash::make($password)
        ]);
    }
}
