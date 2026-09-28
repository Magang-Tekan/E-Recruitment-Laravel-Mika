<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'company_id', 'department_id', 'position_id', 'reviewer_id', 'title', 'description', 
        'employment_type', 'location', 'salary_min', 'salary_max', 
        'quota', 'deadline', 'status'
    ];

    /**
     * Scope lowongan yang benar-benar aktif (status Open dan belum lewat batas tanggal).
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Open')
            ->where(function ($q) {
                $q->whereNull('deadline')
                  ->orWhere('deadline', '>=', now()->toDateString());
            });
    }

    /**
     * Scope lowongan yang sudah melewati batas tanggal pendaftaran.
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('deadline')
            ->where('deadline', '<', now()->toDateString());
    }

    /**
     * Accessor: Cek apakah lowongan sudah kedaluwarsa berdasarkan tanggal deadline.
     */
    public function getIsExpiredAttribute(): bool
    {
        if (!$this->deadline) {
            return false;
        }

        return Carbon::parse($this->deadline)->startOfDay()->lt(now()->startOfDay());
    }

    /**
     * Accessor: Cek apakah lowongan aktif dan dapat dilamar.
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'Open' && !$this->is_expired;
    }

    /**
     * Accessor: Hitung sisa hari sebelum batas lamaran.
     * Mengembalikan:
     * - int >= 0 jika masih dalam rentang deadline
     * - null jika tanpa deadline atau sudah lewat
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->deadline || $this->is_expired) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays(Carbon::parse($this->deadline)->startOfDay(), false);
    }

    /**
     * Accessor: Status deadline deskriptif untuk badge/UI.
     */
    public function getDeadlineBadgeAttribute(): array
    {
        if (!$this->deadline) {
            return [
                'status' => 'no_deadline',
                'label' => 'Hingga Terpenuhi',
                'color' => 'gray',
            ];
        }

        if ($this->is_expired || $this->status === 'Closed') {
            return [
                'status' => 'closed',
                'label' => 'Ditutup',
                'color' => 'red',
            ];
        }

        $days = $this->days_remaining;

        if ($days === 0) {
            return [
                'status' => 'today',
                'label' => 'Berakhir Hari Ini!',
                'color' => 'rose',
            ];
        }

        if ($days !== null && $days <= 3) {
            return [
                'status' => 'urgent',
                'label' => "Sisa {$days} Hari Lagi!",
                'color' => 'amber',
            ];
        }

        return [
            'status' => 'open',
            'label' => 'Batas: ' . Carbon::parse($this->deadline)->format('d M Y'),
            'color' => 'emerald',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function degrees()
    {
        return $this->belongsToMany(Degree::class, 'job_degrees');
    }

    public function majors()
    {
        return $this->belongsToMany(Major::class, 'job_majors');
    }

    public function tests()
    {
        return $this->belongsToMany(Test::class, 'job_test', 'job_id', 'test_id')
                    ->withTimestamps();
    }
}
