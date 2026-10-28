<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array for API consumers (Next.js / Nuxt.js).
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'employment_type' => $this->employment_type,
            'location' => $this->location,
            'salary' => [
                'min' => $this->salary_min !== null ? (float) $this->salary_min : null,
                'max' => $this->salary_max !== null ? (float) $this->salary_max : null,
                'currency' => 'IDR',
                'formatted' => $this->formatSalaryRange(),
            ],
            'quota' => $this->quota,
            'description' => $this->description,
            'deadline' => $this->deadline,
            'status' => $this->status,
            'apply_url' => url('/lowongan/' . $this->id),
            'deadline_info' => [
                'is_expired' => $this->is_expired,
                'is_active' => $this->is_active,
                'days_remaining' => $this->days_remaining,
                'badge' => $this->deadline_badge,
            ],
            'company' => $this->whenLoaded('company', function () {
                return $this->company ? [
                    'id' => $this->company->id,
                    'name' => $this->company->name,
                    'logo' => $this->company->logo ? (str_starts_with($this->company->logo, 'http') ? $this->company->logo : asset('storage/' . $this->company->logo)) : null,
                    'website' => $this->company->website,
                    'city' => $this->company->city,
                    'province' => $this->company->province,
                ] : null;
            }),
            'department' => $this->whenLoaded('department', function () {
                return $this->department ? [
                    'id' => $this->department->id,
                    'name' => $this->department->name,
                ] : null;
            }),
            'position' => $this->whenLoaded('position', function () {
                return $this->position ? [
                    'id' => $this->position->id,
                    'name' => $this->position->name,
                ] : null;
            }),
            'education_requirements' => [
                'degrees' => $this->whenLoaded('degrees', function () {
                    return $this->degrees ? $this->degrees->map(fn ($d) => [
                        'id' => $d->id,
                        'name' => $d->name,
                    ]) : [];
                }),
                'majors' => $this->whenLoaded('majors', function () {
                    return $this->majors ? $this->majors->map(fn ($m) => [
                        'id' => $m->id,
                        'name' => $m->name,
                    ]) : [];
                }),
            ],
        ];
    }

    /**
     * Format rentang gaji ke rupiah untuk kemudahan UI consumer.
     */
    private function formatSalaryRange(): ?string
    {
        if ($this->salary_min && $this->salary_max) {
            return 'Rp ' . number_format($this->salary_min, 0, ',', '.') . ' - Rp ' . number_format($this->salary_max, 0, ',', '.');
        } elseif ($this->salary_min) {
            return 'Mulai Rp ' . number_format($this->salary_min, 0, ',', '.');
        } elseif ($this->salary_max) {
            return 'Hingga Rp ' . number_format($this->salary_max, 0, ',', '.');
        }

        return null;
    }
}
