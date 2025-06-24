<?php

namespace App\Interfaces;

use App\Enums\NotifyChannel;

interface NotificationFactoryInterface
{
    public function makeNotification(NotifyChannel $channel): string;
}
