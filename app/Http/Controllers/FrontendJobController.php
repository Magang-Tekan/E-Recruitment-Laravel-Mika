<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Company;
use App\Models\Department;
use App\Models\JobApplication;
use App\Models\ApplicantProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FrontendJobController extends Controller
{
    /**
     * Display a listing of jobs on a dedicated page with filters and pagination.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $location = $request->query('location');

        // Helper to extract array parameters flexibly (supports array, comma-separated, or single value)
        $getArrayParam = function ($key) use ($request) {
            $val = $request->query($key);
            if (is_array($val)) {
                return array_values(array_filter($val, fn($v) => !is_null($v) && $v !== ''));
            }
            if (is_string($val) && trim($val) !== '') {
                return array_values(array_filter(explode(',', $val), fn($v) => trim($v) !== ''));
            }
            return [];
        };

        $selectedCompanyIds = $getArrayParam('company_id');
        if (empty($selectedCompanyIds)) {
            $selectedCompanyIds = $getArrayParam('company_ids');
        }

        $selectedDepartmentIds = $getArrayParam('department_id');
        if (empty($selectedDepartmentIds)) {
            $selectedDepartmentIds = $getArrayParam('department_ids');
        }

        $selectedEmploymentTypes = $getArrayParam('employment_type');
        if (empty($selectedEmploymentTypes)) {
            $selectedEmploymentTypes = $getArrayParam('employment_types');
        }

        $jobsQuery = Job::active()->with(['company', 'department', 'position', 'degrees', 'majors']);

        if ($search) {
            $searchLower = mb_strtolower($search);
            $jobsQuery->where(function ($q) use ($searchLower) {
                $q->whereRaw('LOWER(title) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(location) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(employment_type) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereHas('company', function ($cq) use ($searchLower) {
                      $cq->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"]);
                  })
                  ->orWhereHas('department', function ($dq) use ($searchLower) {
                      $dq->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"]);
                  })
                  ->orWhereHas('position', function ($pq) use ($searchLower) {
                      $pq->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"]);
                  });
            });
        }

        if ($location) {
            $jobsQuery->where('location', 'like', "%{$location}%");
        }

        if (!empty($selectedDepartmentIds)) {
            $jobsQuery->whereIn('department_id', $selectedDepartmentIds);
        }

        if (!empty($selectedCompanyIds)) {
            $jobsQuery->whereIn('company_id', $selectedCompanyIds);
        }

        if (!empty($selectedEmploymentTypes)) {
            $jobsQuery->where(function ($q) use ($selectedEmploymentTypes) {
                foreach ($selectedEmploymentTypes as $type) {
                    $q->orWhere(function ($subQ) use ($type) {
                        if ($type === 'Magang' || $type === 'Internship') {
                            $subQ->whereIn('employment_type', ['Magang', 'Internship']);
                        } elseif ($type === 'Full Time' || $type === 'Full-time') {
                            $subQ->whereIn('employment_type', ['Full Time', 'Full-time']);
                        } elseif ($type === 'Part Time' || $type === 'Part-time') {
                            $subQ->whereIn('employment_type', ['Part Time', 'Part-time']);
                        } elseif ($type === 'Kontrak' || $type === 'Contract') {
                            $subQ->whereIn('employment_type', ['Kontrak', 'Contract']);
                        } else {
                            $subQ->where('employment_type', $type);
                        }
                    });
                }
            });
        }

        $jobs = $jobsQuery->latest('id')->paginate(6)->withQueryString();
        $departments = Cache::remember('home_departments_with_jobs', 3600, function () {
            return Department::withCount(['jobs' => fn($q) => $q->active()])->get();
        });
        $companies = Cache::remember('companies_with_jobs', 3600, function () {
            return Company::withCount(['jobs' => fn($q) => $q->active()])->get();
        });
        $employmentTypes = [
            'Magang' => 'Magang / Internship',
            'Full Time' => 'Full Time',
            'Part Time' => 'Part Time',
            'Contract' => 'Kontrak / Contract',
            'Freelance' => 'Freelance',
            'Remote' => 'Remote'
        ];

        // For backwards compatibility in views
        $departmentId = count($selectedDepartmentIds) === 1 ? $selectedDepartmentIds[0] : null;
        $companyId = count($selectedCompanyIds) === 1 ? $selectedCompanyIds[0] : null;
        $employmentType = count($selectedEmploymentTypes) === 1 ? $selectedEmploymentTypes[0] : null;

        return view('frontend.jobs.index', compact(
            'jobs',
            'departments',
            'companies',
            'employmentTypes',
            'search',
            'location',
            'selectedCompanyIds',
            'selectedDepartmentIds',
            'selectedEmploymentTypes',
            'departmentId',
            'companyId',
            'employmentType'
        ));
    }

    /**
     * Display the specified job detail page.
     */
    public function show(string $id)
    {
        $job = Job::with(['company', 'department', 'position', 'degrees', 'majors'])
            ->findOrFail($id);

        $relatedJobs = Job::active()
            ->with(['company', 'department', 'position'])
            ->where('id', '!=', $id)
            ->where(function ($q) use ($job) {
                $q->where('department_id', $job->department_id)
                  ->orWhere('company_id', $job->company_id);
            })
            ->take(3)
            ->get();

        $hasApplied = false;
        $isMandatoryComplete = false;
        $missingMandatorySections = [];
        $applicantProfile = null;

        if (auth()->check()) {
            $applicantProfile = ApplicantProfile::where('user_id', auth()->id())->first();
            if ($applicantProfile) {
                $hasApplied = JobApplication::where('job_id', $job->id)
                    ->where('profile_id', $applicantProfile->id)
                    ->exists();

                $isMandatoryComplete = $applicantProfile->is_mandatory_complete && $applicantProfile->completion_percentage >= 80;
                $missingMandatorySections = $applicantProfile->missing_mandatory_sections;
            } else {
                $missingMandatorySections = [
                    'Data Pribadi',
                    'Dokumen CV',
                    'Data Keluarga',
                    'Pendidikan',
                    'Skill / Keahlian',
                ];
            }
        }

        return view('frontend.jobs.show', compact(
            'job',
            'relatedJobs',
            'hasApplied',
            'isMandatoryComplete',
            'missingMandatorySections',
            'applicantProfile'
        ));
    }
}
