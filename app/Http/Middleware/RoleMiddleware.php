<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (empty($roles)) {
            abort(403);
        }

        $roleName = $user->role?->name;
        $allowedRoles = array_map('strtolower', $roles);

        if ($user->role_id == 1 || ($roleName && in_array(strtolower($roleName), $allowedRoles, true))) {
            return $next($request);
        }

        // Izinkan user dengan toggle is_recruiter aktif untuk mengakses rute recruiter
        if ($user->is_recruiter && in_array('recruiter', $allowedRoles, true)) {
            return $next($request);
        }

        abort(403);
    }
}
