<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\JobApplication;
use App\Models\ApplicantProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class CandidateProfilePdfController extends Controller
{
    /**
     * Preview or download candidate complete profile as PDF.
     */
    public function downloadCandidatePdf($id)
    {
        $this->authorizeAdminOrRecruiter();

        $candidate = User::with([
            'applicantProfile.educations',
            'applicantProfile.workExperiences',
            'applicantProfile.organizations',
            'applicantProfile.achievements',
            'applicantProfile.certifications',
            'applicantProfile.trainings',
            'applicantProfile.skills',
            'applicantProfile.socialMedias',
            'applicantProfile.languages',
            'applicantProfile.family',
            'applicantProfile.jobPreference',
            'applicantProfile.jobApplications.job.company'
        ])->findOrFail($id);

        $profile = $candidate->applicantProfile;
        if (!$profile) {
            abort(404, 'Data profil pelamar belum dibuat oleh kandidat ini.');
        }

        $pdf = Pdf::loadView('pdf.candidate_profile', [
            'candidate' => $candidate,
            'profile'   => $profile,
            'isApplicant' => false,
            'application' => null,
            'title'     => 'Profil & CV Lengkap - ' . ($profile->full_name ?? $candidate->name)
        ])->setPaper('a4', 'portrait')->setOption([
            'defaultFont' => 'helvetica',
            'isRemoteEnabled' => true,
        ]);

        $filename = 'Profil_' . Str::slug($profile->full_name ?? $candidate->name) . '.pdf';

        if (request()->query('download') == '1') {
            return $pdf->download($filename);
        }

        $response = $pdf->stream($filename);
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        return $response;
    }

    /**
     * Preview or download job application complete profile as PDF.
     */
    public function downloadApplicantPdf($id)
    {
        $this->authorizeAdminOrRecruiter();

        $application = JobApplication::with([
            'applicantProfile.user',
            'applicantProfile.educations',
            'applicantProfile.workExperiences',
            'applicantProfile.organizations',
            'applicantProfile.achievements',
            'applicantProfile.certifications',
            'applicantProfile.trainings',
            'applicantProfile.skills',
            'applicantProfile.socialMedias',
            'applicantProfile.languages',
            'applicantProfile.family',
            'applicantProfile.jobPreference',
            'job.company',
            'job.department'
        ])->findOrFail($id);

        $profile = $application->applicantProfile;
        if (!$profile) {
            abort(404, 'Data profil pelamar tidak ditemukan.');
        }

        $candidate = $profile->user ?? new User(['name' => $profile->full_name, 'email' => $profile->email]);

        $pdf = Pdf::loadView('pdf.candidate_profile', [
            'candidate' => $candidate,
            'profile'   => $profile,
            'isApplicant' => true,
            'application' => $application,
            'title'     => 'Data Pelamar - ' . ($profile->full_name ?? $candidate->name)
        ])->setPaper('a4', 'portrait')->setOption([
            'defaultFont' => 'helvetica',
            'isRemoteEnabled' => true,
        ]);

        $filename = 'Pelamar_' . Str::slug($profile->full_name ?? $candidate->name) . '_' . Str::slug($application->job?->title ?? 'Lamaran') . '.pdf';

        if (request()->query('download') == '1') {
            return $pdf->download($filename);
        }

        $response = $pdf->stream($filename);
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        return $response;
    }

    protected function authorizeAdminOrRecruiter()
    {
        if (app()->runningInConsole() && !auth()->check()) {
            return;
        }

        $user = auth()->user();
        if (!$user) {
            abort(401, 'Silakan login terlebih dahulu.');
        }

        $isAdmin = ($user->role_id == 1 || in_array(strtolower($user->role?->name ?? ''), ['admin', 'superadmin']));
        $isRecruiter = ($user->role_id == 2 || strtolower($user->role?->name ?? '') === 'recruiter' || (bool) $user->is_recruiter);

        if (!$isAdmin && !$isRecruiter) {
            abort(403, 'Akses ditolak. Fitur ini hanya dapat diakses oleh Admin atau Recruiter.');
        }
    }
}
