<?php

namespace App\Livewire\Applicant;

use App\Models\ApplicantJobPreference;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ApplicantSidebar extends Component
{
    public $activeTab = 'pribadi';
    public $showJobSearchStatusModal = false;

    // Status pencari kerja form properties
    public $job_search_status = 'Aktif';
    public $notification_period = 'Mingguan';
    public $activeSubTab = 'status'; // 'status' or 'notification'

    public function mount($activeTab = 'pribadi')
    {
        $this->activeTab = $activeTab;
        $this->loadJobSearchStatus();
    }

    #[On('profile-updated')]
    public function refreshSidebar()
    {
        $this->loadJobSearchStatus();
    }

    public function loadJobSearchStatus()
    {
        $user = Auth::user();
        if ($user && $user->applicantProfile) {
            $pref = $user->applicantProfile->jobPreference;
            if ($pref) {
                $this->job_search_status = $pref->job_search_status ?: 'Aktif';
                $this->notification_period = $pref->notification_period ?: 'Mingguan';
            }
        }
    }

    public function openJobSearchStatusModal()
    {
        $this->loadJobSearchStatus();
        $this->activeSubTab = 'status';
        $this->showJobSearchStatusModal = true;
    }

    public function closeJobSearchStatusModal()
    {
        $this->showJobSearchStatusModal = false;
    }

    public function updatedJobSearchStatus($value)
    {
        if ($value === 'Tidak Aktif') {
            // When status is 'Tidak Aktif', disabled/reset notification_period or switch to status tab
            if ($this->activeSubTab === 'notification') {
                $this->activeSubTab = 'status';
            }
        }
    }

    public function saveJobSearchStatus()
    {
        $user = Auth::user();
        if (!$user) return;

        $profile = $user->applicantProfile()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        ApplicantJobPreference::updateOrCreate(
            ['applicant_profile_id' => $profile->id],
            [
                'job_search_status' => $this->job_search_status,
                'notification_period' => $this->job_search_status === 'Tidak Aktif' ? null : $this->notification_period,
            ]
        );

        $this->showJobSearchStatusModal = false;
        $this->dispatch('profile-updated', message: __('Status pencari kerja berhasil diperbarui.'), action: 'update');
    }

    public function render()
    {
        $user = Auth::user();
        if ($user) {
            $user->unsetRelation('applicantProfile');
        }
        $applicantProfile = $user ? $user->applicantProfile()->first() : null;
        if ($applicantProfile) {
            $applicantProfile->unsetRelation('socialMedias');
            $applicantProfile->unsetRelation('skills');
            $applicantProfile->unsetRelation('certifications');
            $applicantProfile->unsetRelation('trainings');
            $applicantProfile->unsetRelation('languages');
            $applicantProfile->unsetRelation('educations');
            $applicantProfile->unsetRelation('workExperiences');
            $applicantProfile->unsetRelation('organizations');
            $applicantProfile->unsetRelation('achievements');
            $applicantProfile->unsetRelation('family');
        }

        $completionPercentage = $applicantProfile ? $applicantProfile->completion_percentage : 0;
        $mandatoryPercentage = $applicantProfile ? $applicantProfile->mandatory_completion_percentage : 0;
        $isMandatoryComplete = $applicantProfile ? $applicantProfile->is_mandatory_complete : false;
        $missingMandatorySections = $applicantProfile ? $applicantProfile->missing_mandatory_sections : [];
        $sectionStatuses = $applicantProfile ? $applicantProfile->section_statuses : [];
        $applicationCount = $applicantProfile ? $applicantProfile->jobApplications()->count() : 0;

        return view('components.sidebar.applicant-sidebar', [
            'applicantProfile' => $applicantProfile,
            'completionPercentage' => $completionPercentage,
            'mandatoryPercentage' => $mandatoryPercentage,
            'isMandatoryComplete' => $isMandatoryComplete,
            'missingMandatorySections' => $missingMandatorySections,
            'sectionStatuses' => $sectionStatuses,
            'applicationCount' => $applicationCount,
        ]);
    }
}
