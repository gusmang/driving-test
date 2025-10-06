<?php

namespace App\Jobs;

use App\Mail\SendPin;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPinJob implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;
    public $user;
    public $pin;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $pin)
    {
        //
        $this->user = $user;
        $this->pin = $pin;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        Mail::to($this->user->email)->send(new SendPin($this->user, $this->pin));
    }
}
