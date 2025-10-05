<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Mail\VerifyEmail;
use App\Mail\SendPin;
use App\Mail\ResetPasswordLink;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;

class AuthService
{
    public function __construct(protected UserRepository $repo) {}

    public function register(array $payload)
    {
        $role = $payload['role'];
        $email = $payload['email'];
        if ($this->repo->findByEmail($email)) {
            throw new \Exception('EMAIL_EXISTS');
        }
        if ($role === 'student') {
            $pin = $this->generatePin();
            $user = $this->repo->create([
                'email' => $email,
                'role' => 'student',
                'pin' => $pin,
            ]);
            Mail::to($email)->send(new SendPin($user, $pin));
            // also send verify link (can generate signed url)
            Mail::to($email)->send(new VerifyEmail($user));
            return ['user' => $user, 'pin' => $pin];
        }
        // partner
        if (empty($payload['password'])) throw new \Exception('PASSWORD_REQUIRED');
        $user = $this->repo->create([
            'email' => $email,
            'role' => 'partner',
            'password' => Hash::make($payload['password']),
        ]);
        Mail::to($email)->send(new VerifyEmail($user));
        return ['user' => $user];
    }

    protected function generatePin(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle(str_repeat($chars, 6)), 0, 6);
    }

    public function loginByEmail(string $email, string $password)
    {
        $user = $this->repo->findByEmail($email);
        if (!$user || !$user->password || !Hash::check($password, $user->password)) {
            throw new \Exception('INVALID_CREDENTIALS');
        }
        if (!$user->is_active) throw new \Exception('USER_DISABLED');
        $token = $user->createToken('access-token')->accessToken;
        return ['user' => $user, 'token' => $token];
    }

    public function loginByPin(string $pin)
    {
        $user = User::where('pin', $pin)->where('role', 'student')->first();
        if (!$user) throw new \Exception('INVALID_PIN');
        if (!$user->is_active) throw new \Exception('USER_DISABLED');
        $token = $user->createToken('access-token')->accessToken;
        return ['user' => $user, 'token' => $token];
    }

    public function forgot(string $email)
    {
        $user = $this->repo->findByEmail($email);
        if (!$user) throw new \Exception('NOT_FOUND');
        if ($user->role === 'student') {
            $newPin = $this->generatePin();
            $this->repo->update($user, ['pin' => $newPin]);
            Mail::to($email)->send(new SendPin($user, $newPin));
            return ['type' => 'pin'];
        }
        // admin/partner -> send reset link (signed token)
        $token = Str::random(64);
        // store token in cache/db or use Password::broker -> here illustrate sending
        Mail::to($email)->send(new ResetPasswordLink($user, $token));
        return ['type' => 'link', 'token' => $token];
    }

    public function resetPassword(string $token, string $newPassword)
    {
        // token verification step omitted here - assume valid
        // find user by token mapping (implementation detail)
        throw_if(true, \Exception::class, 'IMPLEMENT_TOKEN_STORE');
    }

    public function logout($user)
    {
        // Revoke current access token
        $token = $user->token();
        if ($token) {
            $token->revoke();
        }
    }
}
