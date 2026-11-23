<?php

namespace App\Http\Controllers;

use App\Models\ApplicantProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicantCvDownloadController extends Controller
{
    /**
     * Tampilkan (preview) file CV pelamar dengan nama file asli saat diunggah.
     * Karena file dikirim inline dengan nama asli, tombol unduh di preview
     * browser akan memakai nama file yang sama.
     */
    public function preview(Request $request, ApplicantProfile $profile, ?string $filename = null)
    {
        $user = $request->user();

        $isStaff = $user && ($user->role_id == 1 || $user->isAdmin() || $user->isRecruiter() || $user->is_recruiter);

        abort_unless($isStaff || ($user && (int) $profile->user_id === (int) $user->id), 403);

        $path = $profile->cv_file_path;
        abort_unless($path && Storage::disk('public')->exists($path), 404);

        $name = $profile->cv_file_name;

        if (! $name) {
            $base = $profile->full_name ?: 'Pelamar';
            $name = 'CV ' . $base . '.' . pathinfo($path, PATHINFO_EXTENSION);
        }

        // Buang karakter yang tidak valid pada nama file.
        $name = trim(preg_replace('/[\\\\\/:\*\?"<>|]+/', '-', $name), ' .-');

        if ($name === '') {
            $name = 'CV.' . pathinfo($path, PATHINFO_EXTENSION);
        }

        return Storage::disk('public')->response($path, $name, [
            'Content-Disposition' => 'inline; filename="' . addslashes($name) . '"',
        ]);
    }
}
