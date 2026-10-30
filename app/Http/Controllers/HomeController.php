<?php

namespace App\Http\Controllers;

use App\Models\CareerTrack;
use App\Models\Company;
use App\Models\CompanyShowcase;
use App\Models\Department;
use App\Models\Job;
use App\Models\Setting;
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

        $companies = Cache::remember('home_companies_with_jobs', 3600, function () {
            return Company::with(['showcases' => function ($q) {
                $q->where('is_active', true);
            }])->withCount(['jobs' => function ($q) {
                $q->active();
            }])->get()->sortBy(function ($comp) {
                $name = strtolower($comp->name);
                return (str_contains($name, 'mitra karya analitika') || str_contains($name, 'mika')) ? 0 : 1;
            })->values();
        });

        $locale = app()->getLocale();
        $showcases = Cache::remember('frontend_company_showcases_' . $locale, 86400, function () {
            return CompanyShowcase::active()->latest()->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'tag' => $item->localized_tag,
                    'title' => $item->localized_title,
                    'description' => $item->localized_description ?? '',
                    'img1' => $item->img1_url,
                    'img2' => $item->img2_url,
                    'img3' => $item->img3_url,
                    'badgeTitle' => $item->localized_badge_title,
                    'badgeSub' => $item->localized_badge_sub ?? '',
                ];
            })->values()->toArray();
        });

        $careerTracks = Cache::remember('frontend_career_tracks_' . $locale, 3600, function () {
            return CareerTrack::active()->get()->map(function ($track) {
                return [
                    'id' => $track->id,
                    'num' => $track->num,
                    'badge' => $track->localized_badge,
                    'prefix' => $track->localized_prefix,
                    'accent' => $track->localized_accent,
                    'desc' => $track->localized_description,
                    'is_dark' => (bool) $track->is_dark,
                    'img' => $track->image_url ?: 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=600&auto=format&fit=crop&q=80',
                    'back_badge' => $track->localized_back_badge,
                    'back_title' => $track->localized_back_title,
                    'highlights' => $track->localized_highlights,
                    'cta' => $track->localized_cta_text,
                    'url' => $track->target_url,
                    'experience_level' => $track->experience_level,
                ];
            })->values()->toArray();
        });

        $jobCountsByLevel = Cache::remember('home_job_counts_by_level', 3600, function () {
            return Job::active()
                ->selectRaw('experience_level, count(*) as total')
                ->whereNotNull('experience_level')
                ->groupBy('experience_level')
                ->pluck('total', 'experience_level')
                ->toArray();
        });

        $heroSettings = Cache::remember('frontend_hero_settings_' . $locale, 86400, function () use ($locale) {
            $isEn = ($locale === 'en');

            $prefix = Setting::get($isEn ? 'hero_title_prefix_en' : 'hero_title_prefix')
                ?: ($isEn ? Setting::get('hero_title_prefix') : null)
                ?: ($isEn ? 'Find Your Dream Career,' : 'Temukan Karir Impian,');

            $highlight = Setting::get($isEn ? 'hero_title_highlight_en' : 'hero_title_highlight')
                ?: ($isEn ? Setting::get('hero_title_highlight') : null)
                ?: ($isEn ? 'Realize Your Best Potential' : 'Wujudkan Potensi Terbaikmu');

            $description = Setting::get($isEn ? 'hero_description_en' : 'hero_description')
                ?: ($isEn ? Setting::get('hero_description') : null)
                ?: ($isEn
                    ? 'Join thousands of talented professionals. Explore job openings, take integrated online tests, and achieve your future career now.'
                    : 'Bergabunglah bersama ribuan profesional bertalenta. Jelajahi lowongan kerja, ikuti tes seleksi online terintegrasi, dan raih karir masa depan sekarang.');

            $bgImage = Setting::get('hero_background_image') ?: 'https://i.pinimg.com/736x/20/dc/f0/20dcf0cfb0122a38d938e3c3011fb0d4.jpg';
            if (!str_starts_with($bgImage, 'http://') && !str_starts_with($bgImage, 'https://')) {
                $bgImage = asset('storage/' . $bgImage);
            }

            return [
                'prefix' => $prefix,
                'highlight' => $highlight,
                'description' => $description,
                'bg_image' => $bgImage,
            ];
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
            'careerTracks',
            'jobCountsByLevel',
            'heroSettings',
            'search',
            'location'
        ));
    }
}
