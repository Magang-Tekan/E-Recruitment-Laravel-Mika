<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeProfile extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_CONTRACT_ENDED = 'contract_ended';

    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'user_id',
        'nik',
        'full_name',
        'company_id',
        'department_id',
        'position_id',
        'position_title',
        'employee_type',
        'employment_status',
        'contract_start_date',
        'contract_end_date',
        'phone_number',
        'gender',
        'birth_date',
        'photo',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
    ];

    public function employmentStatusLabel(): string
    {
        return match ($this->employment_status) {
            self::STATUS_CONTRACT_ENDED => 'Kontrak Berakhir',
            self::STATUS_INACTIVE => 'Nonaktif',
            default => 'Aktif',
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
}
