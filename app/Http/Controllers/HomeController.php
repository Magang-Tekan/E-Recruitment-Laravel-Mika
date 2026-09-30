<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Company;
use App\Models\CompanyShowcase;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Display the dedicated Recruitment Home Page (Beranda).
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $location = $request->query('location');

        $hasFilter = !empty($search) || !empty($location);

        if ($hasFilter) {
            $jobsQuery = Job::active()->with(['company', 'department', 'degrees', 'majors']);

            if ($search) {
                $jobsQuery->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('company', function ($cq) use ($search) {
                          $cq->where('name', 'like', "%{$search}%");
                      });
                });
            }

            if ($location) {
                $jobsQuery->where('location', 'like', "%{$location}%");
            }

            $featuredJobs = $jobsQuery->latest('id')->take(6)->get();
        } else {
            $featuredJobs = Cache::remember('home_featured_jobs', 3600, function () {
                return Job::active()
                    ->with(['company', 'department', 'degrees', 'majors'])
                    ->latest('id')
                    ->take(6)
                    ->get();
            });
        }

        // Cached home statistics & master catalogs
        $totalJobsCount = Cache::remember('home_total_jobs_count', 3600, fn() => Job::active()->count());
        $companiesCount = Cache::remember('home_companies_count', 86400, fn() => Company::count());
        $departmentsCount = Cache::remember('home_departments_count', 86400, fn() => Department::count());
        $totalQuotaCount = Cache::remember('home_total_quota_count', 3600, fn() => Job::active()->sum('quota'));
        
        $departments = Cache::remember('home_departments_with_jobs', 3600, function () {
            return Department::withCount(['jobs' => function ($q) {
                $q->active();
            }])->get();
        });

        $companies = Cache::remember('master_companies_all', 86400, fn() => Company::all());

        $showcases = Cache::remember('frontend_company_showcases', 86400, function () {
            return CompanyShowcase::active()->latest()->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tag' => $item->tag ?? 'Acara & Kolaborasi',
                    'title' => $item->title,
                    'description' => $item->description ?? '',
                    'img1' => $item->img1_url,
                    'img2' => $item->img2_url,
                    'img3' => $item->img3_url,
                    'badgeTitle' => $item->badge_title ?? 'EVENT',
                    'badgeSub' => $item->badge_sub ?? '',
                ];
            })->values()->toArray();
        });

        return view('frontend.home.index', compact(
            'featuredJobs',
            'totalJobsCount',
            'companiesCount',
            'departmentsCount',
            'totalQuotaCount',
            'departments',
            'companies',
            'showcases',
            'search',
            'location'
        ));
    }
}
