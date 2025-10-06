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
    public function __construct(protected AuthService $service) {}

    public function register(RegisterRequest $req)
    {
        $payload = $req->validated();
        $out = $this->service->register($payload);

        if (isset($out['error'])) {
            return response()->json([
                'status' => 'ERROR',
                'code' => 400,
                'message' => $out['error']
            ], 400);
        }

        $user = $out['user'];
        return response()->json([
            'status' => 'SUCCESS',
            'code' => 200,
            'result' => [
                'id' => $user->id,
                'email' => $user->email,
                'isVerified' => $user->is_verified,
                'pin' => $user->role === 'student' ? ($out['pin'] ?? null) : null
            ]
        ], 200);
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
                'code' => 200,
                'result' => [
                    'id' => $out['user']->id,
                    'email' => $out['user']->email,
                    'isVerified' => $out['user']->is_verified,
                    'access_token' => $out['token']
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'FAIL', 'code' => 401, 'message' => $e->getMessage()], 401);
        }
    }

    public function forgot(ForgotRequest $req)
    {
        $out = $this->service->forgot($req->email);
        return response()->json(['status' => 'SUCCESS', 'code' => 200, 'result' => $out], 200);
    }

    public function reset(ResetRequest $req)
    {
        // implement using Password broker - simplified here
        // after reset -> respond success
        $out = $this->service->resetPassword($req->token, $req->password);
        return response()->json(['status' => 'SUCCESS', 'code' => 200, 'result' => $out]);
    }


    public function me(Request $req)
    {
        $user = $req->user();
        return response()->json(['status' => 'SUCCESS', 'code' => 200, 'result' => [
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'profile' => [
                'firstName' => $user->first_name,
                'lastName' => $user->last_name
            ]
        ]], 200);
    }

    public function verify(FacadesRequest $request, $id, $email)
    {
        // panggil service verify
        return $this->service->verify($request, $id, $email);
    }

    public function logout(Request $req)
    {
        $this->service->logout($req->user());
        return response()->json(['status' => 'SUCCESS', 'code' => 200, 'result' => ['message' => 'logged out']], 200);
    }
}
