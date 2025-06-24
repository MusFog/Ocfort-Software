<?php

namespace App\Observers;

use App\DTOs\ReportData;
use App\Enums\EmailType;
use App\Enums\SmsType;
use App\Interfaces\NotificationsServiceInterface;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function __construct(
        private NotificationsServiceInterface $notificationsService
    ) {}

    public function created(User $user): void
    {
        $userId = $user->id;
        $reportData = new ReportData(null);
        $runAt = config('send_mail.user_action');
        $this->notificationsService->sendEmail($userId, EmailType::USER_REGISTRATION, $reportData, $runAt);
        $this->notificationsService->sendSms($userId, SmsType::USER_REGISTRATION, $reportData, $runAt);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
