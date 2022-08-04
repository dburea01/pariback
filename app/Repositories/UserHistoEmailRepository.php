<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\UserHistoEmail;

class UserHistoEmailRepository
{
    public function insert(string $userId, string $emailType): UserHistoEmail
    {
        $userHistoEmail = new UserHistoEmail();
        $userHistoEmail->user_id = $userId;
        $userHistoEmail->email_type = $emailType;
        $userHistoEmail->sent_at = now();
        $userHistoEmail->save();

        return $userHistoEmail;
    }

    public function userHistoEmailOfTheDay(string $userId, string $emailType): int
    {
        $userHistoEmail = UserHistoEmail::where('user_id', $userId)
                ->where('email_type', $emailType)
                ->whereDate('sent_at', date('Y-m-d'))
                ->get();

        return count($userHistoEmail);
    }
}
