<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'job_id', 
        'profile_id', 
        'status', 
        'applied_at', 
        'notes',
        'recruiter_approval',
        'recruiter_notes',
        'recruiter_approved_at',
        'recruiter_id',
        'admin_approval',
        'admin_notes',
        'admin_approved_at',
        'admin_id',
        'test_recruiter_approval',
        'test_recruiter_notes',
        'test_recruiter_approved_at',
        'test_recruiter_id',
        'test_admin_approval',
        'test_admin_notes',
        'test_admin_approved_at',
        'test_admin_id',
        'interview_recruiter_approval',
        'interview_recruiter_notes',
        'interview_recruiter_approved_at',
        'interview_recruiter_id',
        'interview_admin_approval',
        'interview_admin_notes',
        'interview_admin_approved_at',
        'interview_admin_id',
    ];

    protected $casts = [
        'applied_at'                      => 'datetime',
        'recruiter_approved_at'           => 'datetime',
        'admin_approved_at'               => 'datetime',
        'test_recruiter_approved_at'      => 'datetime',
        'test_admin_approved_at'          => 'datetime',
        'interview_recruiter_approved_at' => 'datetime',
        'interview_admin_approved_at'     => 'datetime',
    ];

    protected $appends = ['status_label'];

    public function getStatusLabelAttribute(): string
    {
        return match (strtolower(trim($this->status ?? ''))) {
            'submitted', 'applied', 'pending' => __('Diajukan'),
            'reviewed'                        => __('Lolos Berkas'),
            'shortlisted'                     => __('Lolos Ujian'),
            'interview'                       => __('Wawancara'),
            'accepted', 'hired'               => __('Diterima'),
            'rejected'                        => __('Ditolak'),
            'partial approved'                => __('Partial Approved'),
            default                           => $this->status ? __($this->status) : __('Diajukan'),
        };
    }

    public function recruiter()
    {
        return $this->belongsTo(User::class, 'recruiter_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function testRecruiter()
    {
        return $this->belongsTo(User::class, 'test_recruiter_id');
    }

    public function testAdmin()
    {
        return $this->belongsTo(User::class, 'test_admin_id');
    }

    public function interviewRecruiter()
    {
        return $this->belongsTo(User::class, 'interview_recruiter_id');
    }

    public function interviewAdmin()
    {
        return $this->belongsTo(User::class, 'interview_admin_id');
    }

    public function isDoubleApproved(): bool
    {
        return $this->recruiter_approval === 'approved' && $this->admin_approval === 'approved';
    }

    public function isPartialApproved(): bool
    {
        return ($this->recruiter_approval === 'approved' && $this->admin_approval === 'pending')
            || ($this->admin_approval === 'approved' && $this->recruiter_approval === 'pending');
    }

    public function isTestDoubleApproved(): bool
    {
        return $this->test_recruiter_approval === 'approved' && $this->test_admin_approval === 'approved';
    }

    public function isTestPartialApproved(): bool
    {
        return ($this->test_recruiter_approval === 'approved' && $this->test_admin_approval !== 'approved')
            || ($this->test_admin_approval === 'approved' && $this->test_recruiter_approval !== 'approved');
    }

    public function isInterviewDoubleApproved(): bool
    {
        return $this->interview_recruiter_approval === 'approved' && $this->interview_admin_approval === 'approved';
    }

    public function isInterviewPartialApproved(): bool
    {
        return ($this->interview_recruiter_approval === 'approved' && $this->interview_admin_approval !== 'approved')
            || ($this->interview_admin_approval === 'approved' && $this->interview_recruiter_approval !== 'approved');
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function applicantProfile()
    {
        return $this->belongsTo(ApplicantProfile::class, 'profile_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(ApplicationStatusHistory::class, 'job_applications_id');
    }

    public function interviewSchedules()
    {
        return $this->hasMany(InterviewSchedule::class, 'job_applications_id');
    }

    public function testAttempts()
    {
        return $this->hasMany(TestAttempt::class, 'job_application_id');
    }
}
