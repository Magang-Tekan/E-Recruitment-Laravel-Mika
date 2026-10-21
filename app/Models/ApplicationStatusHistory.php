<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationStatusHistory extends Model
{
    protected $table = 'application_status_history';
    protected $fillable = ['job_applications_id', 'status', 'notes', 'changed_by', 'changed_at'];
    public $timestamps = false; // Menggunakan changed_at sesuai skema

    protected $casts = [
        'changed_at' => 'datetime',
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

    public function jobApplication()
    {
        return $this->belongsTo(JobApplication::class, 'job_applications_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
