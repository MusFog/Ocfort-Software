<?php

namespace App\Jobs;

use App\DTOs\ReportData;
use App\Enums\EmailType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\User;


class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public EmailType $typeEmail;
    public ReportData $report;
    public $runAt;

    public function __construct(
        $userId,
        EmailType $typeEmail,
        ReportData $report,
        $runAt = null
    )
    {
        $this->userId = $userId;
        $this->typeEmail = $typeEmail;
        $this->report = $report;
        $this->runAt = $runAt;

        if ($runAt) {
            $this->delay($runAt);
        }
    }

    public function handle()
    {
        $user = User::whereId($this->userId)->first();
        $email = $user->email;

        $mailClass = $this->typeEmail->getMailClass();

        Mail::to($email)->send(new $mailClass($user, $this->report));
    }
}
