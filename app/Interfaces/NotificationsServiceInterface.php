<?php

namespace App\Interfaces;

use App\DTOs\ReportData;
use App\Enums\EmailType;
use App\Enums\SmsType;

interface NotificationsServiceInterface
{
    public function sendEmail(string $userId, EmailType $emailType, ReportData $report, int $runAt): void;
    public function sendSms(string $userId, SmsType $smsType, ReportData $report, int $runAt): void;
}
