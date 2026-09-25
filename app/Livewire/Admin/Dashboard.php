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
        $isRecruiter = $user && ($user->role_id == 2 || strtolower($user->role?->name ?? '') === 'recruiter');

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
            $totalJobs = Job::whereIn(DB::raw('LOWER(status)'), ['open', 'published', 'active', 'draft'])
                ->where(function($q) {
                    $q->whereNull('deadline')->orWhere('deadline', '>=', now()->toDateString());
                })->count();
            $activeJobs = $totalJobs;

            $totalApplicants = JobApplication::whereHas('job', function ($j) {
                $j->whereIn(DB::raw('LOWER(status)'), ['open', 'published', 'active', 'draft'])
                  ->where(function($q) {
                      $q->whereNull('deadline')->orWhere('deadline', '>=', now()->toDateString());
                  });
            })->count();

            $pendingReview = JobApplication::whereHas('job', function ($j) {
                $j->whereIn(DB::raw('LOWER(status)'), ['open', 'published', 'active', 'draft'])
                  ->where(function($q) {
                      $q->whereNull('deadline')->orWhere('deadline', '>=', now()->toDateString());
                  });
            })->whereIn('status', ['applied', 'pending', 'screening', 'submitted'])->count();

            $totalQuestions = 0;
            $totalTests = 0;

            // Recent applications for active jobs only
            $recentApplications = JobApplication::with(['job', 'applicantProfile.user'])
                ->whereHas('job', function ($j) {
                    $j->whereIn(DB::raw('LOWER(status)'), ['open', 'published', 'active', 'draft'])
                      ->where(function($q) {
                          $q->whereNull('deadline')->orWhere('deadline', '>=', now()->toDateString());
                      });
                })
                ->orderBy('id', 'desc')
                ->take(5)
                ->get();

            // Active Jobs for recruiter
            $recentJobs = Job::with(['company', 'department'])
                ->withCount('jobApplications')
                ->whereIn(DB::raw('LOWER(status)'), ['open', 'published', 'active', 'draft'])
                ->where(function($q) {
                    $q->whereNull('deadline')->orWhere('deadline', '>=', now()->toDateString());
                })
                ->orderBy('id', 'desc')
                ->take(5)
                ->get();

            $statusCounts = JobApplication::whereHas('job', function ($j) {
                $j->whereIn(DB::raw('LOWER(status)'), ['open', 'published', 'active', 'draft'])
                  ->where(function($q) {
                      $q->whereNull('deadline')->orWhere('deadline', '>=', now()->toDateString());
                  });
            })
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
        } else {
            $totalJobs = Job::count();
            $activeJobs = Job::whereIn(DB::raw('LOWER(status)'), ['open', 'active', 'published'])->count();
            $totalApplicants = JobApplication::count();
            $pendingReview = JobApplication::whereIn('status', ['applied', 'pending', 'screening', 'submitted'])->count();
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
            $statusCounts = JobApplication::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();
        }

        return view('livewire.admin.dashboard', [
            'isAdmin' => !$isRecruiter,
            'isRecruiter' => $isRecruiter,
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
