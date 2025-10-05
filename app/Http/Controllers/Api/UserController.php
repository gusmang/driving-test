<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected UserService $service)
    {
        $this->middleware('ensure.admin'); // middleware custom
    }

    public function index(Request $req)
    {
        $pageIndex = $req->query('pageIndex');
        $pageSize = $req->query('pageSize');
        if (!$pageIndex && !$pageSize) {
            $all = $this->service->getAllStudents();
            $result = $all->map(fn($u) => ['id' => $u->id, 'email' => $u->email, 'profile' => []]);
            return response()->json(['status' => 'SUCCESS', 'code' => 200, 'result' => $result], 200);
        }
        $pag = $this->service->getStudentsPaginated([
            'keyword' => $req->query('keyword'),
            'pageIndex' => intval($pageIndex ?? 1),
            'pageSize' => intval($pageSize ?? 10),
            'sortId' => $req->query('sortId'),
            'sortOrder' => $req->query('sortOrder')
        ]);
        $result = $pag->items();
        return response()->json([
            'status' => 'SUCCESS',
            'code' => 200,
            'result' => array_map(fn($u) => ['id' => $u->id, 'email' => $u->email, 'profile' => []], $result),
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
