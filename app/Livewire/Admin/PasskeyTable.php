<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Company;
use App\Models\Setting;
use App\Livewire\Traits\WithTableSkeleton;

class PasskeyTable extends Component
{
    use WithPagination, WithTableSkeleton;

    // Global Master Passkey State
    public $globalPasskey = '';
    public $showGlobalPasskeyText = true;

    // Per-Company Table State
    public $search = '';
    public $filterStatus = ''; // '' (all) | 'custom' | 'default'
    public $perPage = 10;

    // Modal Edit Company Passkey State
    public $showCompanyModal = false;
    public $editingCompanyId = null;
    public $editingCompanyName = '';
    public $companyPasskey = '';
    public $showCompanyPasskeyText = true;

    public function mount()
    {
        $this->globalPasskey = Setting::get('employee_registration_passkey', env('EMPLOYEE_REGISTRATION_PASSKEY', 'MIKA2026'));
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function toggleGlobalVisibility()
    {
        $this->showGlobalPasskeyText = !$this->showGlobalPasskeyText;
    }

    public function generateGlobalPasskey()
    {
        $this->globalPasskey = 'MIKA' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
    }

    public function saveGlobalPasskey()
    {
        $this->validate([
            'globalPasskey' => 'required|string|min:4|max:50',
        ], [
            'globalPasskey.required' => 'Master passkey global tidak boleh kosong.',
            'globalPasskey.min' => 'Master passkey minimal 4 karakter.',
            'globalPasskey.max' => 'Master passkey maksimal 50 karakter.',
        ]);

        Setting::set('employee_registration_passkey', trim($this->globalPasskey), 'Master Passkey default registrasi akun karyawan');

        session()->flash('message', 'Master Passkey Global berhasil diperbarui menjadi: ' . trim($this->globalPasskey));
    }

    public function openCompanyModal($companyId)
    {
        $company = Company::findOrFail($companyId);
        $this->editingCompanyId = $company->id;
        $this->editingCompanyName = $company->name;
        $this->companyPasskey = $company->registration_passkey ?? '';
        $this->showCompanyPasskeyText = true;
        $this->resetErrorBag('companyPasskey');
        $this->showCompanyModal = true;
    }

    public function closeCompanyModal()
    {
        $this->showCompanyModal = false;
        $this->editingCompanyId = null;
        $this->editingCompanyName = '';
        $this->companyPasskey = '';
        $this->resetErrorBag('companyPasskey');
    }

    public function toggleCompanyVisibility()
    {
        $this->showCompanyPasskeyText = !$this->showCompanyPasskeyText;
    }

    public function generateCompanyPasskey()
    {
        $prefix = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', substr($this->editingCompanyName, 0, 4)));
        $this->companyPasskey = ($prefix ?: 'CORP') . '-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
    }

    public function useGlobalDefault()
    {
        $this->companyPasskey = '';
    }

    public function saveCompanyPasskey()
    {
        $this->validate([
            'companyPasskey' => 'nullable|string|max:100',
        ]);

        $company = Company::findOrFail($this->editingCompanyId);
        $cleanPasskey = filled($this->companyPasskey) ? trim($this->companyPasskey) : null;
        $company->update([
            'registration_passkey' => $cleanPasskey,
        ]);

        $this->closeCompanyModal();

        if ($cleanPasskey) {
            session()->flash('message', "Passkey khusus untuk {$company->name} berhasil disetel menjadi: {$cleanPasskey}");
        } else {
            session()->flash('message', "{$company->name} kini kembali menggunakan Master Passkey Global.");
        }
    }

    public function render()
    {
        $companies = Company::query()
            ->when($this->search, function ($query) {
                $search = strtolower(trim($this->search));
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(city) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(registration_passkey) LIKE ?', ['%' . $search . '%']);
                });
            })
            ->when($this->filterStatus === 'custom', function ($query) {
                $query->whereNotNull('registration_passkey')->where('registration_passkey', '!=', '');
            })
            ->when($this->filterStatus === 'default', function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('registration_passkey')->orWhere('registration_passkey', '');
                });
            })
            ->orderBy('id', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.passkey.table', [
            'companies' => $companies,
            'activeMasterPasskey' => Setting::get('employee_registration_passkey', env('EMPLOYEE_REGISTRATION_PASSKEY', 'MIKA2026')),
            'showGlobalPasskeyText' => $this->showGlobalPasskeyText,
            'globalPasskey' => $this->globalPasskey,
            'showCompanyModal' => $this->showCompanyModal,
            'editingCompanyName' => $this->editingCompanyName,
            'companyPasskey' => $this->companyPasskey,
            'showCompanyPasskeyText' => $this->showCompanyPasskeyText,
        ]);
    }
}
