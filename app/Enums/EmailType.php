<?php

namespace App\Enums;

enum EmailType
{
    case USER_REGISTRATION;
    case CATEGORY_SET;

    public function getMailClass(): string
    {
        return match($this) {
            self::USER_REGISTRATION => \App\Mail\UserRegistration::class,
            self::CATEGORY_SET  => \App\Mail\CategoryCreated::class,
        };
    }
}
