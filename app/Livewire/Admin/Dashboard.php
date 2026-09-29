<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\TestAttempt;
use App\Models\QuestionBank;
use App\Models\Test;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Livewire\Traits\WithTableSkeleton;

class Dashboard extends Component
{
    /**
     * Placeholder khusus dashboard skeleton saat komponen Livewire di-load secara lazy.
     */
    public function placeholder()
    {
        return view('components.skeleton.dashboard');
    }

    public function render()
    {
        $user = auth()->user();
        $isAdmin = $user && ($user->role_id == 1 || strtolower($user->role?->name ?? '') === 'admin');
        $isRecruiter = $user && !$isAdmin && ($user->role_id == 2 || strtolower($user->role?->name ?? '') === 'recruiter' || (bool) $user->is_recruiter);
        $isDesignatedRecruiter = $user && ((bool) $user->is_recruiter && !$isAdmin && $user->role_id != 2);

        $totalCandidates = User::where(function ($q) {
            $q->where('role_id', 3)
              ->orWhereHas('role', function ($rq) {
                  $rq->whereRaw('LOWER(name) = ?', ['applicant']);
              })
              ->orWhereHas('applicantProfile');
        })
        ->whereNotIn('role_id', [1, 2])
        ->whereDoesntHave('role', function ($rq) {
            $rq->whereIn(DB::raw('LOWER(name)'), ['admin', 'superadmin', 'recruiter']);
        })
        ->count();

        if ($isRecruiter) {
            $jobsBaseQuery = Job::query()
                ->when($isDesignatedRecruiter, function ($q) use ($user) {
                    $q->where('reviewer_id', $user->id);
                })
                ->when(!$isDesignatedRecruiter, function ($q) {
                    $q->whereIn(DB::raw('LOWER(status)'), ['open', 'published', 'active', 'draft'])
                      ->where(function($sq) {
                          $sq->whereNull('deadline')->orWhere('deadline', '>=', now()->toDateString());
                      });
                });

            $totalJobs = (clone $jobsBaseQuery)->count();
            $activeJobs = $totalJobs;

            $applicationsBaseQuery = JobApplication::whereHas('job', function ($j) use ($isDesignatedRecruiter, $user) {
                if ($isDesignatedRecruiter) {
                    $j->where('reviewer_id', $user->id);
                } else {
                    $j->whereIn(DB::raw('LOWER(status)'), ['open', 'published', 'active', 'draft'])
                      ->where(function($sq) {
                          $sq->whereNull('deadline')->orWhere('deadline', '>=', now()->toDateString());
                      });
                }
            });

            $totalApplicants = (clone $applicationsBaseQuery)->count();

            $pendingReview = (clone $applicationsBaseQuery)
                ->whereIn(DB::raw('LOWER(status)'), ['applied', 'pending', 'screening', 'submitted'])
                ->count();

            $totalQuestions = 0;
            $totalTests = 0;

            // Recent applications for active jobs only
            $recentApplications = (clone $applicationsBaseQuery)
                ->with(['job', 'applicantProfile.user'])
                ->orderBy('id', 'desc')
                ->take(5)
                ->get();

            // Active Jobs for recruiter
            $recentJobs = (clone $jobsBaseQuery)
                ->with(['company', 'department'])
                ->withCount('jobApplications')
                ->orderBy('id', 'desc')
                ->take(5)
                ->get();

            $statusCounts = (clone $applicationsBaseQuery)
                ->select(DB::raw('LOWER(status) as lower_status'), DB::raw('count(*) as total'))
                ->groupBy(DB::raw('LOWER(status)'))
                ->pluck('total', 'lower_status')
                ->toArray();
        } else {
            $totalJobs = Job::count();
            $activeJobs = Job::whereIn(DB::raw('LOWER(status)'), ['open', 'active', 'published'])->count();
            $totalApplicants = JobApplication::count();
            $pendingReview = JobApplication::whereIn(DB::raw('LOWER(status)'), ['applied', 'pending', 'screening', 'submitted'])->count();
            $totalQuestions = QuestionBank::count();
            $totalTests = Test::count();

            // Recent applications
            $recentApplications = JobApplication::with(['job', 'applicantProfile.user'])
                ->orderBy('id', 'desc')
                ->take(5)
                ->get();

            // Active / Recent Jobs
            $recentJobs = Job::with(['company', 'department'])
                ->withCount('jobApplications')
                ->orderBy('id', 'desc')
                ->take(5)
                ->get();

            // Status breakdown
            $statusCounts = JobApplication::select(DB::raw('LOWER(status) as lower_status'), DB::raw('count(*) as total'))
                ->groupBy(DB::raw('LOWER(status)'))
                ->pluck('total', 'lower_status')
                ->toArray();
        }

        return view('livewire.admin.dashboard', [
            'isAdmin' => $isAdmin,
            'isRecruiter' => $isRecruiter,
            'isDesignatedRecruiter' => $isDesignatedRecruiter,
            'totalJobs' => $totalJobs,
            'activeJobs' => $activeJobs,
            'totalApplicants' => $totalApplicants,
            'pendingReview' => $pendingReview,
            'totalQuestions' => $totalQuestions,
            'totalTests' => $totalTests,
            'totalCandidates' => $totalCandidates,
            'recentApplications' => $recentApplications,
            'recentJobs' => $recentJobs,
            'statusCounts' => $statusCounts,
        ]);
    }
}
