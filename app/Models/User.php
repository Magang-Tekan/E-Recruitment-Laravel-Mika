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
     * Check if user is a recruiter (role recruiter or employee with is_recruiter toggle).
     */
    public function isRecruiter(): bool
    {
        return $this->role_id == 2 || strtolower($this->role?->name ?? '') === 'recruiter' || (bool) $this->is_recruiter;
    }

    /**
     * Check if user can review job applicants.
     */
    public function canReview(): bool
    {
        return $this->isAdmin() || $this->isRecruiter();
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

    public function getAvatarUrlAttribute(): ?string
    {
        if (! empty($this->avatar)) {
            return \Illuminate\Support\Str::startsWith($this->avatar, ['http://', 'https://'])
                ? $this->avatar
                : asset('storage/' . $this->avatar);
        }

        return null;
    }

    protected static function booted(): void
    {
        static::updated(function (User $user) {
            // Sinkronkan profil saat role diubah menjadi Employee dari User Management.
            if ($user->wasChanged('role_id')
                && ($user->role_id == 4 || strtolower($user->role?->name ?? '') === 'employee')) {
                // Kolom nik unik di employee_profiles; hindari kegagalan simpan bila sudah dipakai.
                $nik = $user->nik;
                if (! $nik || EmployeeProfile::where('nik', $nik)->where('user_id', '!=', $user->id)->exists()) {
                    $nik = null;
                }

                EmployeeProfile::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nik'       => $nik,
                        'full_name' => $user->name,
                        'photo'     => $user->avatar ?? null,
                    ]
                );
            }
        });

        static::created(function (User $user) {
            if ($user->role_id == 3 || strtolower($user->role?->name ?? '') === 'applicant') {
                // photo tidak diisi dari avatar (mis. foto bawaan Google); foto profil
                // pelamar hanya terisi setelah pelamar mengunggah foto formal sendiri.
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
                        'photo'     => $user->avatar ?? null,
                    ]
                );
            }
        });
    }
}
