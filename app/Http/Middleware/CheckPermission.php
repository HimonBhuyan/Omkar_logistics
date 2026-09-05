<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permissionKey)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->hasPermission($permissionKey)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => "You do not have access permission to '{$permissionKey}'."
            ], 403);
        }

        return response()->view('errors.403', [
            'permissionKey' => $permissionKey,
        ], 403);
    }
}
