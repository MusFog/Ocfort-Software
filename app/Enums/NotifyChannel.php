<?php

namespace App\Enums;

enum NotifyChannel: string
{
    case Mail = 'mail';
    case Sms  = 'sms';
}
