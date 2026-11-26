<?php

namespace App\Http\Middleware;

use App\Models\EmployeeProfile;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployeeAccountIsActive
{
    /**
     * Blokir akses akun karyawan yang status kepegawaiannya 'inactive'.
     * Status 'contract_ended' tetap boleh masuk (menunggu keputusan HR).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (
            $user
            && (int) $user->role_id === 4
            && $user->employeeProfile?->employment_status === EmployeeProfile::STATUS_INACTIVE
        ) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'form.email' => 'Akun Anda sedang dinonaktifkan. Hubungi Admin/HR untuk mengaktifkan kembali.',
            ]);
        }

        return $next($request);
    }
}
