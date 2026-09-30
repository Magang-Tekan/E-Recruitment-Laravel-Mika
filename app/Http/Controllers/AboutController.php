<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyShowcase;
use App\Models\Department;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AboutController extends Controller
{
    /**
     * Display the About Us (Tentang Kami) page.
     */
    public function index()
    {
        // Prioritas ambil PT Mitra Karya Analitika, atau fallback ke record Company pertama
        $company = Cache::remember('frontend_main_company', 86400, function () {
            return Company::where('name', 'like', '%Mitra Karya Analitika%')
                ->orWhere('name', 'like', '%MIKA%')
                ->first() ?? Company::first();
        });

        // Ambil entitas grup perusahaan lainnya secara dinamis (selain PT Mitra Karya Analitika)
        $groupCompanies = Cache::remember('about_group_companies', 86400, function () use ($company) {
            return Company::when($company, fn($q) => $q->where('id', '!=', $company->id))
                ->where('name', '!=', 'PT Mitra Karya Analitika')
                ->where('name', 'not like', '%Mitra Karya Analitika%')
                ->orderBy('id', 'asc')
                ->get();
        });

        // Data pendukung statistik dan CTA
        $totalJobsCount = Cache::remember('home_total_jobs_count', 3600, fn() => Job::active()->count());
        $departmentsCount = Cache::remember('home_departments_count', 86400, fn() => Department::count());
        $departments = Cache::remember('about_departments_with_jobs', 3600, function () {
            return Department::withCount(['jobs' => function ($q) {
                $q->active();
            }])->take(6)->get();
        });

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

        return view('frontend.about.index', compact(
            'company',
            'groupCompanies',
            'totalJobsCount',
            'departmentsCount',
            'departments',
            'showcases'
        ));
    }
}
