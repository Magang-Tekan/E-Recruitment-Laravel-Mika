<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Livewire\Traits\WithTableSkeleton;

class CandidateTable extends Component
{
    use WithPagination, WithTableSkeleton;

    public $search = '';
    public $perPage = 10;
    public $selectedCandidateId = null;
    public $showDetailModal = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function openDetailModal($candidateId)
    {
        $this->selectedCandidateId = $candidateId;
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedCandidateId = null;
    }

    public function render()
    {
        $user = auth()->user();
        $isAdmin = $user && ($user->role_id == 1 || in_array(strtolower($user->role?->name ?? ''), ['admin', 'superadmin']));
        $isRecruiter = !$isAdmin && ($user->role_id == 2 || strtolower($user->role?->name ?? '') === 'recruiter' || (bool) $user->is_recruiter);
        $userDeptId = $user?->employeeProfile?->department_id;
        $userPosId  = $user?->employeeProfile?->position_id;
        $isDesignatedRecruiter = $user && ((bool) $user->is_recruiter && !$isAdmin && $user->role_id != 2);

        $candidates = User::with([
            'applicantProfile.educations',
            'applicantProfile.workExperiences',
            'applicantProfile.organizations',
            'applicantProfile.achievements',
            'applicantProfile.certifications',
            'applicantProfile.trainings',
            'applicantProfile.skills',
            'applicantProfile.socialMedias',
            'applicantProfile.languages',
            'applicantProfile.family',
            'applicantProfile.jobPreference',
            'applicantProfile.jobApplications.job.company',
            'applicantProfile.jobApplications.job.department'
        ])
        ->where(function ($q) {
            $q->where('role_id', 3)
              ->orWhereHas('role', function ($rq) {
                  $rq->whereRaw('LOWER(name) = ?', ['applicant']);
              })
              ->orWhereHas('applicantProfile');
        })
        ->whereNotIn('role_id', [1, 2])
        ->whereDoesntHave('role', function ($rq) {
            $rq->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(name)'), ['admin', 'superadmin', 'recruiter']);
        })
        ->when($isRecruiter && $user, function ($query) use ($user, $userPosId, $userDeptId, $isDesignatedRecruiter) {
            $query->whereHas('applicantProfile.jobApplications.job', function ($jq) use ($user, $userPosId, $userDeptId, $isDesignatedRecruiter) {
                if ($isDesignatedRecruiter) {
                    $jq->where(function ($sq) use ($user, $userPosId) {
                        $sq->where('reviewer_id', $user->id);
                        if ($userPosId) {
                            $sq->orWhere('position_id', $userPosId);
                        }
                    });
                } elseif ($userDeptId) {
                    $jq->where('department_id', $userDeptId);
                }
            });
        })
        ->when($this->search, function ($query) {
            $search = strtolower(trim($this->search));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(email) LIKE ?', ['%' . $search . '%'])
                  ->orWhereHas('applicantProfile', function ($apq) use ($search) {
                      $apq->whereRaw('LOWER(full_name) LIKE ?', ['%' . $search . '%'])
                          ->orWhereRaw('LOWER(city) LIKE ?', ['%' . $search . '%'])
                          ->orWhereRaw('LOWER(province) LIKE ?', ['%' . $search . '%'])
                          ->orWhereRaw('LOWER(nik) LIKE ?', ['%' . $search . '%'])
                          ->orWhereRaw('LOWER(phone) LIKE ?', ['%' . $search . '%']);
                  });
            });
        })
        ->orderBy('id', 'desc')
        ->paginate($this->perPage);

        return view('livewire.admin.candidates.table', [
            'candidates' => $candidates,
            'isAdmin' => $isAdmin,
            'isRecruiter' => $isRecruiter
        ]);
    }
}
