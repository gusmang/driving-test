<?php

namespace App\Jobs;

use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class SendVerifyEmailJob implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    public $user;
    public $pin;
    //public $url;

    public function __construct(User $user, string $pin)
    {
        $this->user = $user;
        $this->pin = $pin;

        // $this->url = URL::temporarySignedRoute(
        //     'verification.verify',                       // route name
        //     now()->addHours(24),                         // expiration
        //     ['id' => $user->id, 'email' => $user->email] // route params
        // );
    }

    public function handle()
    {
        // Pastikan URL base sesuai, karena queue worker tidak punya request context
        URL::forceRootUrl(config('app.url'));
        URL::forceScheme(parse_url(config('app.url'), PHP_URL_SCHEME) ?: 'http');

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(24),
            ['id' => $this->user->id, 'email' => $this->user->email]
        );

        Mail::to($this->user->email)->send(new VerifyEmail($this->user, $this->pin, $url));
    }
}
