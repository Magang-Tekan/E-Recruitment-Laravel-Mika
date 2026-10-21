<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Degree;
use App\Models\Department;
use App\Models\Job;
use App\Models\Major;
use App\Models\Position;
use App\Models\TestCategory;
use App\Observers\CompanyObserver;
use App\Observers\DegreeObserver;
use App\Observers\DepartmentObserver;
use App\Observers\JobObserver;
use App\Observers\MajorObserver;
use App\Observers\PositionObserver;
use App\Observers\TestCategoryObserver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        date_default_timezone_set(config('app.timezone', 'Asia/Jakarta'));
        \Carbon\Carbon::setLocale(config('app.locale', 'id'));

        // Ensure Eloquent class definitions are preloaded for safe cache unserialization
        class_exists(\Illuminate\Database\Eloquent\Collection::class);
        class_exists(\Illuminate\Database\Eloquent\Model::class);
        class_exists(\App\Models\Company::class);
        class_exists(\App\Models\Department::class);
        class_exists(\App\Models\Job::class);
        class_exists(\App\Models\Degree::class);
        class_exists(\App\Models\Major::class);
        class_exists(\App\Models\Position::class);
        class_exists(\App\Models\TestCategory::class);

        if (!app()->runningInConsole()) {
            if (request()->isSecure() || request()->header('X-Forwarded-Proto') === 'https' || str_contains(config('app.url'), 'https://')) {
                URL::forceScheme('https');
            }
        } elseif (str_contains(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Register Model Observers for Automatic Cache Invalidation
        Degree::observe(DegreeObserver::class);
        Major::observe(MajorObserver::class);
        Department::observe(DepartmentObserver::class);
        Position::observe(PositionObserver::class);
        TestCategory::observe(TestCategoryObserver::class);
        Company::observe(CompanyObserver::class);
        Job::observe(JobObserver::class);

        // Optimized View Composer with Query Caching
        View::composer(['frontend.*', 'frontend.components.footer', 'frontend.components.navbar', 'layouts.guest', 'livewire.pages.auth.*'], function ($view) {
            $footerDepartments = Cache::remember('frontend_footer_departments', 86400, function () {
                return Department::withCount(['jobs' => fn($q) => $q->where('status', 'Open')])
                    ->take(5)
                    ->get();
            });
            
            $mainCompany = Cache::remember('frontend_main_company', 86400, function () {
                return Company::where('name', 'like', '%Mitra Karya Analitika%')
                    ->orWhere('name', 'like', '%MIKA%')
                    ->first() ?? Company::first();
            });

            $view->with([
                'footerDepartments' => $footerDepartments,
                'mainCompany' => $mainCompany,
            ]);
        });
    }
}
