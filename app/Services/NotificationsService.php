<?php

namespace App\Services;

use App\DTOs\ReportData;
use App\Enums\EmailType;
use App\Enums\NotifyChannel;
use App\Enums\SmsType;
use App\Interfaces\NotificationFactoryInterface;
use App\Interfaces\NotificationsServiceInterface;

class NotificationsService implements NotificationsServiceInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private NotificationFactoryInterface $notificationFactory,
    )
    {

    }

    public function sendEmail(string $userId, EmailType $emailType, ReportData $report, int $runAt): void
    {
        $sendEmailClass = $this->notificationFactory->makeNotification(NotifyChannel::Mail);
        $sendEmailClass::dispatch($userId, $emailType, $report, now()->addMinutes($runAt));
    }

    public function sendSms(string $userId, SmsType $smsType, ReportData $report, int $runAt): void
    {
        $sendSmsClass = $this->notificationFactory->makeNotification(NotifyChannel::Sms);
        $sendSmsClass::dispatch($userId, $smsType, $report, now()->addMinutes($runAt));
    }
}
