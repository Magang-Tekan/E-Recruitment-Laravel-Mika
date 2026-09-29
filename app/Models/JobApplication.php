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
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'recruiter_approved_at' => 'datetime',
        'admin_approved_at' => 'datetime',
    ];

    public function recruiter()
    {
        return $this->belongsTo(User::class, 'recruiter_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
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
