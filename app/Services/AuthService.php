<?php

namespace App\Services;

use App\Jobs\SendPinJob;
use App\Jobs\SendResetPasswordLinkJob;
use App\Jobs\SendVerifyEmailJob;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Mail\ResetPasswordLink;
use App\Mail\VerifyEmail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

class AuthService
{
    public function __construct(protected UserRepository $repo) {}

    public function register(array $payload)
    {
        $role = $payload['role'] ?? null;
        $email = $payload['email'] ?? null;
        $first_name = $payload['first_name'] ?? null;
        $last_name = $payload['last_name'] ?? null;

        // validasi basic
        if (!$role || !$email || !$first_name || !$last_name) {
            return ['error' => 'MISSING_REQUIRED_FIELDS'];
        }

        if ($this->repo->findByEmail($email)) {
            return ['error' => 'EMAIL_EXISTS'];
        }

        if ($role === 'student') {
            $pin = $this->generatePin();
            $user = $this->repo->create([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => strtolower($email),
                'role' => 'student',
                'pin' => $pin,
            ]);

            // kirim email PIN & verification link
            try {
                //Mail::to($email)->send(new VerifyEmail($user, $pin));
                dispatch(new SendVerifyEmailJob($user, $pin));
            } catch (\Exception $e) {
                return [
                    'error' => $e->getMessage(),
                    'messagew' => $e->getMessage()
                ];
            }

            return ['user' => $user, 'pin' => $pin];
        }

        // partner
        if (empty($payload['password'])) {
            return ['error' => 'PASSWORD_REQUIRED'];
        }

        $user = $this->repo->create([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'role' => 'partner',
            'password' => Hash::make($payload['password']),
        ]);

        try {
            //Mail::to($email)->send(new VerifyEmail($user, ""));
            dispatch(new SendVerifyEmailJob($user, ""));
        } catch (\Exception $e) {
            return [
                'error' => 'EMAIL_SEND_FAILED',
                'messagep' => $e->getMessage()
            ];
        }

        return ['user' => $user];
    }

    public function verify(Request $request, $id, $email)
    {
        $user = $this->repo->findById($id);

        if (!$user || $user->email !== $email) {
            return response()->json([
                'status' => 'ERROR',
                'code' => 404,
                'message' => 'User not found or invalid verification link'
            ], 404);
        }

        if ($user->is_verified) {
            // Sudah verified, bisa redirect ke SPA
            return Redirect::to(env('FRONTEND_URL') . '/login?verified=1');
        }

        // Tandai user sudah verified
        $user->is_verified = true;
        $user->save();

        // Redirect ke SPA (misal halaman login)
        return Redirect::to(env('FRONTEND_URL') . '/login?verified=1');
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
        $user = User::where('pin', $pin)
            ->where('role', 'student')
            ->where('is_verified', true)
            ->first();

        if (!$user) throw new \Exception('INVALID_PIN');
        if (!$user->is_active) throw new \Exception('USER_DISABLED');
        $token = $user->createToken('access-token')->accessToken;
        return ['user' => $user, 'token' => $token];
    }

    public function forgot(string $email)
    {
        $user = $this->repo->findByEmail($email);

        if (!$user) {
            return [
                'status' => 'ERROR',
                'code' => 404,
                'message' => 'Email not found'
            ];
        }

        if ($user->role === 'student') {
            $newPin = $this->generatePin();
            $this->repo->update($user, ['pin' => $newPin]);
            dispatch(new SendPinJob($user, $newPin));
            return ['status' => 'SUCCESS', 'type' => 'pin'];
        }

        $token = Str::random(64);
        dispatch(new SendResetPasswordLinkJob($user, $token));
        return ['status' => 'SUCCESS', 'type' => 'link', 'token' => $token];
    }

    public function resetPassword(string $token, string $newPassword)
    {
        // token verification step omitted here - assume valid
        // find user by token mapping (implementation detail)
        $reset = DB::table('password_reset_tokens')->where('created_at', '>=', now()->subHours(2))->get();

        foreach ($reset as $row) {
            if (Hash::check($token, $row->token)) {
                $user = $this->repo->findByEmail($row->email);
                $user->password = Hash::make($newPassword);
                $user->save();

                DB::table('password_reset_tokens')->where('email', $row->email)->delete();
                return ['status' => 'success'];
            }
        }

        throw new \Exception('INVALID_OR_EXPIRED_TOKEN');
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
