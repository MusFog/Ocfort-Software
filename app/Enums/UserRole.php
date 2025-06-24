<?php

namespace App\Enums;
use BenSampo\Enum\Enum;

final class UserRole extends Enum
{
    public const ADMIN = 2;
    public const MODERATOR = 1;
    public const USER = 0;
}

