<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\DegreeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeTestController;
use App\Http\Controllers\FrontendCompanyController;
use App\Http\Controllers\FrontendJobController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InterviewScheduleController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\QuestionBankController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TestCategoryController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TestEvaluationController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\RoleMiddleware;
use App\Livewire\Applicant\ApplicantOnlineTest;
use App\Livewire\Employee\EmployeeOnlineTest;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('tentang-kami', [AboutController::class, 'index'])->name('about');
Route::get('perusahaan/{id}', [FrontendCompanyController::class, 'show'])->name('companies.show');
Route::get('lowongan', [FrontendJobController::class, 'index'])->name('jobs.index');
Route::get('lowongan/{id}', [FrontendJobController::class, 'show'])->name('jobs.show');
Route::post('lowongan/{id}/apply', [JobApplicationController::class, 'store'])->middleware(['auth'])->name('jobs.apply');

// Session Heartbeat (Sliding Refresh di balik layar saat pengguna aktif)
Route::post('session/keep-alive', function () {
    return response()->json(['status' => 'active', 'timestamp' => time()]);
})->middleware(['auth'])->name('session.keep_alive');

// Multi-user dashboard redirect based on role
Route::get('dashboard', function () {
    $user = auth()->user();
    if (! $user) {
        return redirect()->route('login');
    }

    $roleName = strtolower($user->role?->name ?? '');

    if ($user->role_id == 1 || in_array($roleName, ['admin', 'superadmin'])) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->role_id == 2 || $roleName === 'recruiter') {
        return redirect()->route('recruiter.dashboard');
    }

    if ($user->role_id == 4 || $roleName === 'employee') {
        return redirect()->route('employee.dashboard');
    }

    // Role 3 or applicant/pelamar
    return redirect()->route('profile');
})->middleware(['auth'])->name('dashboard');

// Employee Group Routes (/employee/*)
Route::middleware(['auth', 'verified', RoleMiddleware::class.':employee'])
    ->prefix('employee')
    ->name('employee.')
    ->group(function () {
        Route::view('dashboard', 'profile')->name('dashboard');
    });

// Recruiter Group Routes (/recruiter/*)
Route::middleware(['auth', 'verified', RoleMiddleware::class.':recruiter'])
    ->prefix('recruiter')
    ->name('recruiter.')
    ->group(function () {
        Route::view('dashboard', 'livewire.admin.dashboard.index')->name('dashboard');
        Route::view('applicants', 'livewire.admin.applicants.index')->name('application');
        Route::put('applicants/{id}', [JobApplicationController::class, 'update'])->name('application.update');
        Route::view('candidates', 'livewire.admin.candidates.index')->name('candidate');
        // Evaluation & Essay Grading / Evaluasi Hasil & Nilai Ujian
        Route::view('test-evaluations', 'livewire.admin.test-evaluation.index')->name('test_evaluation');
        Route::put('test-evaluations/{id}/grade', [TestEvaluationController::class, 'updateGrade'])->name('test_evaluation.grade');
        Route::get('test-evaluations/{id}/disc-pdf', [TestEvaluationController::class, 'downloadDiscPdf'])->name('test_evaluation.disc_pdf');
        Route::get('test-evaluations/{id}/papi-pdf', [TestEvaluationController::class, 'downloadPapiPdf'])->name('test_evaluation.papi_pdf');

        // Employee Assessment Results (Recruiter)
        Route::view('employee-test-evaluations', 'livewire.admin.employee-test-evaluation.index')->name('employee_test_evaluation');

        // Interview Schedules
        Route::view('interview-schedules', 'livewire.admin.interview-schedule.index')->name('interview_schedule');
        Route::post('interview-schedules', [InterviewScheduleController::class, 'store'])->name('interview_schedule.store');
        Route::put('interview-schedules/{id}', [InterviewScheduleController::class, 'update'])->name('interview_schedule.update');
        Route::delete('interview-schedules/{id}', [InterviewScheduleController::class, 'destroy'])->name('interview_schedule.destroy');

        Route::view('profile', 'profile')->name('profile');
    });

// Admin Group Routes (/admin/*)
Route::middleware(['auth', 'verified', RoleMiddleware::class.':admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::view('dashboard', 'livewire.admin.dashboard.index')->name('dashboard');
        Route::view('profile', 'profile')->name('profile');

        // Recruitment Management - Selection CV & Candidate View
        Route::view('applicants', 'livewire.admin.applicants.index')->name('application');
        Route::put('applicants/{id}', [JobApplicationController::class, 'update'])->name('application.update');
        Route::view('candidates', 'livewire.admin.candidates.index')->name('candidate');

        // Interview Schedules
        Route::view('interview-schedules', 'livewire.admin.interview-schedule.index')->name('interview_schedule');
        Route::post('interview-schedules', [InterviewScheduleController::class, 'store'])->name('interview_schedule.store');
        Route::put('interview-schedules/{id}', [InterviewScheduleController::class, 'update'])->name('interview_schedule.update');
        Route::delete('interview-schedules/{id}', [InterviewScheduleController::class, 'destroy'])->name('interview_schedule.destroy');

        // Employee Assessment Management (Admin)
        Route::view('employee-tests', 'livewire.admin.employee-test.index')->name('employee_test');
        Route::post('employee-tests', [EmployeeTestController::class, 'store'])->name('employee_test.store');
        Route::put('employee-tests/{id}', [EmployeeTestController::class, 'update'])->name('employee_test.update');
        Route::delete('employee-tests/{id}', [EmployeeTestController::class, 'destroy'])->name('employee_test.destroy');
        Route::view('employee-test-evaluations', 'livewire.admin.employee-test-evaluation.index')->name('employee_test_evaluation');
        Route::view('employees', 'livewire.admin.employee.index')->name('employee');

        // Admin-Only Routes
        Route::middleware([RoleMiddleware::class.':admin'])->group(function () {
            // User & Role Management
            Route::view('users', 'livewire.admin.user.index')->name('user');
            Route::post('users', [UserController::class, 'store'])->name('user.store');
            Route::put('users/{id}', [UserController::class, 'update'])->name('user.update');
            Route::delete('users/{id}', [UserController::class, 'destroy'])->name('user.destroy');

            Route::view('roles', 'livewire.admin.role.index')->name('role');
            Route::post('roles', [RoleController::class, 'store'])->name('role.store');
            Route::put('roles/{id}', [RoleController::class, 'update'])->name('role.update');
            Route::delete('roles/{id}', [RoleController::class, 'destroy'])->name('role.destroy');

            // Recruitment Management (Jobs)
            Route::view('jobs', 'livewire.admin.jobs.index')->name('job');
            Route::post('jobs', [JobController::class, 'store'])->name('job.store');
            Route::put('jobs/{id}', [JobController::class, 'update'])->name('job.update');
            Route::delete('jobs/{id}', [JobController::class, 'destroy'])->name('job.destroy');

            // Data Master Company
            Route::view('companies', 'livewire.admin.company.index')->name('company');
            Route::post('companies', [CompanyController::class, 'store'])->name('company.store');
            Route::put('companies/{id}', [CompanyController::class, 'update'])->name('company.update');
            Route::delete('companies/{id}', [CompanyController::class, 'destroy'])->name('company.destroy');

            // Data Master Department
            Route::view('departments', 'livewire.admin.department.index')->name('department');
            Route::post('departments', [DepartmentController::class, 'store'])->name('department.store');
            Route::put('departments/{id}', [DepartmentController::class, 'update'])->name('department.update');
            Route::delete('departments/{id}', [DepartmentController::class, 'destroy'])->name('department.destroy');

            // Data Master Position / Jabatan
            Route::view('positions', 'livewire.admin.position.index')->name('position');
            Route::post('positions', [PositionController::class, 'store'])->name('position.store');
            Route::put('positions/{id}', [PositionController::class, 'update'])->name('position.update');
            Route::delete('positions/{id}', [PositionController::class, 'destroy'])->name('position.destroy');

            // Data Master Major / Jurusan
            Route::view('majors', 'livewire.admin.major.index')->name('major');
            Route::post('majors', [MajorController::class, 'store'])->name('major.store');
            Route::put('majors/{id}', [MajorController::class, 'update'])->name('major.update');
            Route::delete('majors/{id}', [MajorController::class, 'destroy'])->name('major.destroy');

            // Data Master Degree / Tingkat Pendidikan
            Route::view('degrees', 'livewire.admin.degree.index')->name('degree');
            Route::post('degrees', [DegreeController::class, 'store'])->name('degree.store');
            Route::put('degrees/{id}', [DegreeController::class, 'update'])->name('degree.update');
            Route::delete('degrees/{id}', [DegreeController::class, 'destroy'])->name('degree.destroy');

            // Data Master Test Category / Kategori Soal
            Route::view('test-categories', 'livewire.admin.test-category.index')->name('test_category');
            Route::post('test-categories', [TestCategoryController::class, 'store'])->name('test_category.store');
            Route::put('test-categories/{id}', [TestCategoryController::class, 'update'])->name('test_category.update');
            Route::delete('test-categories/{id}', [TestCategoryController::class, 'destroy'])->name('test_category.destroy');

            // Data Master Question Bank / Bank Soal
            Route::view('question-banks', 'livewire.admin.question-bank.index')->name('question_bank');
            Route::post('question-banks', [QuestionBankController::class, 'store'])->name('question_bank.store');
            Route::post('question-banks/import', [QuestionBankController::class, 'import'])->name('question_bank.import');
            Route::get('question-banks/download-template', [QuestionBankController::class, 'downloadTemplate'])->name('question_bank.download_template');
            Route::put('question-banks/{id}', [QuestionBankController::class, 'update'])->name('question_bank.update');
            Route::delete('question-banks/{id}', [QuestionBankController::class, 'destroy'])->name('question_bank.destroy');

            // Recruitment Tests / Merakit Paket Ujian
            Route::view('tests', 'livewire.admin.test.index')->name('test');
            Route::post('tests', [TestController::class, 'store'])->name('test.store');
            Route::put('tests/{id}', [TestController::class, 'update'])->name('test.update');
            Route::delete('tests/{id}', [TestController::class, 'destroy'])->name('test.destroy');

            // Evaluation & Essay Grading / Evaluasi Hasil & Nilai Ujian
            Route::view('test-evaluations', 'livewire.admin.test-evaluation.index')->name('test_evaluation');
            Route::put('test-evaluations/{id}/grade', [TestEvaluationController::class, 'updateGrade'])->name('test_evaluation.grade');
            Route::get('test-evaluations/{id}/disc-pdf', [TestEvaluationController::class, 'downloadDiscPdf'])->name('test_evaluation.disc_pdf');
            Route::get('test-evaluations/{id}/papi-pdf', [TestEvaluationController::class, 'downloadPapiPdf'])->name('test_evaluation.papi_pdf');
        });
    });

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('test-evaluations/{id}/disc-pdf', [TestEvaluationController::class, 'downloadDiscPdf'])
    ->middleware(['auth'])
    ->name('test_evaluation.disc_pdf');

Route::get('test-evaluations/{id}/papi-pdf', [TestEvaluationController::class, 'downloadPapiPdf'])
    ->middleware(['auth'])
    ->name('test_evaluation.papi_pdf');

Route::get('employee/test/{testId}', EmployeeOnlineTest::class)
    ->middleware(['auth'])
    ->name('employee.test');

Route::get('profile/test/{applicationId}/{testId?}', ApplicantOnlineTest::class)
    ->middleware(['auth'])
    ->name('applicant.test');

Route::get('profile/cv/preview', [CvController::class, 'preview'])
    ->middleware(['auth'])
    ->name('profile.cv.preview');

require __DIR__.'/auth.php';
