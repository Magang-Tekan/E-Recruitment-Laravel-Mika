<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role_id', 'is_recruiter', 'nik', 'google_id', 'avatar'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Determine if the user has set a local password.
     */
    public function hasPassword(): bool
    {
        return ! empty($this->password);
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role_id == 1 || strtolower($this->role?->name ?? '') === 'admin';
    }

    /**
     * Check if user can review job applicants.
     */
    public function canReview(): bool
    {
        return $this->isAdmin() || $this->role_id == 2 || (bool) $this->is_recruiter;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_recruiter' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function applicantProfile()
    {
        return $this->hasOne(ApplicantProfile::class, 'user_id');
    }

    public function employeeProfile()
    {
        return $this->hasOne(EmployeeProfile::class, 'user_id');
    }

    public function testAttempts()
    {
        return $this->hasMany(TestAttempt::class, 'user_id');
    }

    protected static function booted(): void
    {
        static::created(function (User $user) {
            if ($user->role_id == 3 || strtolower($user->role?->name ?? '') === 'applicant') {
                ApplicantProfile::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nik'       => $user->nik ?? null,
                        'full_name' => $user->name ?? null,
                    ]
                );
            } elseif ($user->role_id == 4 || strtolower($user->role?->name ?? '') === 'employee') {
                EmployeeProfile::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nik'       => $user->nik ?? null,
                        'full_name' => $user->name ?? null,
                    ]
                );
            }
        });
    }
}
