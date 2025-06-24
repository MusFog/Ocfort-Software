<?php

namespace App\Factories;

use App\Enums\NotifyChannel;
use App\Interfaces\NotificationFactoryInterface;
use App\Jobs\SendEmail;
use App\Jobs\SendSms;


class NotificationFactory implements NotificationFactoryInterface
{
    public function makeNotification(NotifyChannel $channel): string
    {
         return match ($channel) {
             NotifyChannel::Mail => SendEmail::class,
             NotifyChannel::Sms  => SendSms::class,
        };
    }
}
