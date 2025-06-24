<?php

namespace App\Enums;

enum SmsType
{
    case USER_REGISTRATION;

    public function getMailClass(): string
    {
        return match($this) {
            self::USER_REGISTRATION => \App\Notifications\UserRegistration::class,
        };
    }
}
