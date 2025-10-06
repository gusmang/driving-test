<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Requests\RegisterRequest;
use App\Requests\LoginRequest;
use App\Requests\ForgotRequest;
use App\Requests\ResetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FacadesRequest;

class AuthController extends Controller
{
    public $success_code;
    public $error_code;

    public function __construct(protected AuthService $service)
    {
        $this->success_code = config('responsecode.success');
        $this->error_code = $this->error_code;
    }

    public function register(RegisterRequest $req)
    {
        $payload = $req->validated();
        $out = $this->service->register($payload);

        if (isset($out['error'])) {
            return response()->json([
                'status' => 'ERROR',
                'code' => $this->error_code,
                'message' => $out['error']
            ], $this->error_code);
        }

        $user = $out['user'];
        return response()->json([
            'status' => 'SUCCESS',
            'code' => $this->success_code,
            'result' => [
                'id' => $user->id,
                'email' => $user->email,
                'isVerified' => $user->is_verified,
                'pin' => $user->role === 'student' ? ($out['pin'] ?? null) : null
            ]
        ], $this->success_code);
    }

    public function login(LoginRequest $req)
    {
        $data = $req->validated();
        try {
            if (!empty($data['pin'])) {
                $out = $this->service->loginByPin($data['pin']);
            } else {
                $out = $this->service->loginByEmail($data['email'], $data['password']);
            }
            return response()->json([
                'status' => 'SUCCESS',
                'code' => $this->success_code,
                'result' => [
                    'id' => $out['user']->id,
                    'email' => $out['user']->email,
                    'isVerified' => $out['user']->is_verified,
                    'access_token' => $out['token']
                ]
            ], $this->success_code);
        } catch (\Exception $e) {
            return response()->json(['status' => 'FAIL', 'code' => config('responsecode.unauthorized'), 'message' => $e->getMessage()], config('responsecode.unauthorized'));
        }
    }

    public function forgot(ForgotRequest $req)
    {
        $out = $this->service->forgot($req->email);
        return response()->json(['status' => 'SUCCESS', 'code' => $this->success_code, 'result' => $out], $this->success_code);
    }

    public function reset(ResetRequest $req)
    {
        // implement using Password broker - simplified here
        // after reset -> respond success
        $out = $this->service->resetPassword($req->token, $req->password);
        return response()->json(['status' => 'SUCCESS', 'code' => $this->success_code, 'result' => $out]);
    }


    public function me(Request $req)
    {
        $user = $req->user();
        return response()->json(['status' => 'SUCCESS', 'code' => $this->success_code, 'result' => [
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'profile' => [
                'firstName' => $user->first_name,
                'lastName' => $user->last_name
            ]
        ]], $this->success_code);
    }

    public function verify(FacadesRequest $request, $id, $email)
    {
        // panggil service verify
        return $this->service->verify($request, $id, $email);
    }

    public function logout(Request $req)
    {
        $this->service->logout($req->user());
        return response()->json(['status' => 'SUCCESS', 'code' => $this->success_code, 'result' => ['message' => 'logged out']], $this->success_code);
    }
}
