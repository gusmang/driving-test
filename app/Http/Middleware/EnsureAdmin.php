<?php

namespace App\Http\Middleware;

use Closure;

class EnsureAdmin
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if (!$user || $user->role !== 'admin') {
            return response()->json(['status' => 'FAIL', 'code' => 403, 'message' => 'Forbidden'], 403);
        }
        return $next($request);
    }
}
