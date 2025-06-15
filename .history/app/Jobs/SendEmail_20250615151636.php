<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserNotification;
use App\Models\User;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $runAt;

    public function __construct($userId, $runAt = null)
    {
        $this->userId = $userId;
        $this->runAt = $runAt;

        if ($runAt) {
            $this->delay($runAt); 
        }
    }

    public function handle()
    {
        $user = User::whereId($this->userId)->first();
        $email = $user->email;
        
        Mail::to($email)->send(new UserNotification($user));
    }
}