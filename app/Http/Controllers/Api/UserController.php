<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Http\Request;

/**
 * @method middleware(string|array $name)
 */
class UserController extends Controller
{

    public function __construct(protected UserService $service)
    {
        // 2. Then, call the middleware method
        // $this->middleware('auth:api');       // This should now work
        // $this->middleware('ensure.admin');
    }

    public function index(Request $req)
    {
        $pageIndex = intval($req->query('pageIndex', 1)); // default 1
        $pageSize = intval($req->query('pageSize', 10));  // default 10

        $pag = $this->service->getStudentsPaginated([
            'keyword' => $req->query('keyword'),
            'pageIndex' => $pageIndex,
            'pageSize' => $pageSize,
            'sortId' => $req->query('sortId'),
            'sortOrder' => $req->query('sortOrder')
        ]);

        $result = $pag->items();

        return response()->json([
            'status' => 'SUCCESS',
            'code' => 200,
            'result' => array_map(fn($u) => [
                'id' => $u->id,
                'email' => $u->email,
                'role' => $u->role,
                'is_verified' => $u->is_verified,
                'profile' => [
                    'firstName' => $u->first_name,
                    'lastName' => $u->last_name
                ]
            ], $result),
            'pagination' => [
                'PageNumber' => $pag->currentPage(),
                'PageSize' => $pag->perPage(),
                'TotalRecords' => $pag->total(),
                'TotalPages' => $pag->lastPage()
            ]
        ], 200);
    }

    public function toggle($id, Request $req)
    {
        $enable = $req->input('action') === 'enable';
        $user = $this->service->enableDisable((int)$id, $enable);
        return response()->json(['status' => 'SUCCESS', 'code' => 200, 'result' => ['id' => $user->id, 'isActive' => $user->is_active]], 200);
    }

    public function destroy($id)
    {
        $user = $this->service->softDelete((int)$id);
        return response()->json(['status' => 'SUCCESS', 'code' => 200, 'result' => ['id' => $user->id]], 200);
    }
}
