<?php

namespace App\Jobs;

use App\Mail\ResetPasswordLink;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class SendResetPasswordLinkJob implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;
    public $user;
    public $token;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $token)
    {
        //
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        Mail::to($this->user->email)->send(new ResetPasswordLink($this->user, $this->token));
    }
}
