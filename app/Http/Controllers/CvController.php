<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CvController extends Controller
{
    /**
     * Preview applicant CV
     */
    public function preview(Request $request)
    {
        $user = auth()->user();
        
        $isAdminOrRecruiter = $user && ($user->isAdmin() || $user->isRecruiter());

        if ($isAdminOrRecruiter) {
            return redirect()->route($user->isRecruiter() && !$user->isAdmin() ? 'recruiter.dashboard' : 'admin.dashboard')
                ->with('error', 'Fitur preview CV pelamar hanya diperuntukkan bagi akun pelamar.');
        }

        $profile = $user->applicantProfile()
            ->with([
                'educations',
                'workExperiences',
                'organizations',
                'achievements',
                'certifications',
                'trainings',
                'skills',
                'socialMedias',
                'languages',
            ])
            ->first();

        return view('profile.cv-preview', compact('user', 'profile'));
    }
}
