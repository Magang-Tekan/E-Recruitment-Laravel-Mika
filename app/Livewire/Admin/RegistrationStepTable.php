<?php

namespace App\Livewire\Admin;

use App\Models\RegistrationStep;
use App\Models\Setting;
use App\Services\TranslationService;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class RegistrationStepTable extends Component
{
    // Section settings
    public $section_title = '';
    public $section_subtitle = '';
    public $section_enabled = true;

    // Form state
    public $showModal = false;
    public $tab = 1;
    public $editingId = null;
    public $deletingId = null;
    public $deletingTitle = '';

    public $short_title = '';
    public $short_sub = '';
    public $eyebrow = '';
    public $title = '';
    public $description = '';
    public $highlightsText = '';
    public $panel_title = '';
    public $tips_title = '';
    public $tips_body = '';
    public $footnote = '';
    public $icon = 'shield';
    public $cta_action = 'register';
    public $cta_action_guest = '';
    public $cta_label = '';
    public $cta_label_guest = '';
    public $cta_label_admin = '';
    public $cta_url = '';
    public $next_label = '';
    public $sort_order = 0;
    public $is_active = true;

    /**
     * Pengelompokan kolom form per tab agar pengisian lebih ringkas.
     */
    private const TAB_FIELDS = [
        1 => ['short_title', 'short_sub', 'eyebrow', 'title', 'description', 'highlightsText'],
        2 => ['panel_title', 'icon', 'tips_title', 'tips_body', 'footnote'],
        3 => ['cta_action', 'cta_action_guest', 'cta_url', 'cta_label', 'cta_label_guest', 'cta_label_admin', 'next_label', 'sort_order', 'is_active'],
    ];

    public function tabHasError(int $tab): bool
    {
        return $this->getErrorBag()->has(self::TAB_FIELDS[$tab] ?? []);
    }

    private function tabForErrors(array $fields): int
    {
        foreach (self::TAB_FIELDS as $tab => $tabFields) {
            if (array_intersect($fields, $tabFields)) {
                return $tab;
            }
        }

        return 1;
    }

    public function mount(): void
    {
        $this->section_title = (string) Setting::get('registration_flow_title', 'Alur Pendaftaran & Seleksi');
        $this->section_subtitle = (string) Setting::get(
            'registration_flow_subtitle',
            'Ikuti 6 tahapan sistematis dan transparan untuk bergabung menjadi bagian dari talenta terbaik PT Mitra Karya Analitika (MIKA).'
        );
        $this->section_enabled = (bool) Setting::get('registration_flow_enabled', true);
    }

    protected function rules(): array
    {
        return [
            'short_title' => 'required|string|max:100',
            'short_sub' => 'nullable|string|max:150',
            'eyebrow' => 'nullable|string|max:180',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'highlightsText' => 'nullable|string',
            'panel_title' => 'nullable|string|max:180',
            'tips_title' => 'nullable|string|max:180',
            'tips_body' => 'nullable|string',
            'footnote' => 'nullable|string',
            'icon' => 'required|in:' . implode(',', RegistrationStep::ICONS),
            'cta_action' => 'required|in:' . implode(',', RegistrationStep::CTA_ACTIONS),
            'cta_action_guest' => 'nullable|in:' . implode(',', RegistrationStep::CTA_ACTIONS),
            'cta_label' => 'nullable|string|max:100',
            'cta_label_guest' => 'nullable|string|max:100',
            'cta_label_admin' => 'nullable|string|max:100',
            'cta_url' => 'nullable|required_if:cta_action,custom|string|max:255',
            'next_label' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'short_title.required' => 'Judul singkat tahap (untuk stepper) wajib diisi.',
            'short_title.max' => 'Judul singkat maksimal 100 karakter.',
            'title.required' => 'Judul utama tahap wajib diisi.',
            'title.max' => 'Judul utama maksimal 200 karakter.',
            'cta_url.required_if' => 'URL tujuan wajib diisi saat memilih aksi "URL Kustom".',
            'icon.required' => 'Pilih ikon panel ketentuan.',
            'icon.in' => 'Ikon yang dipilih tidak valid.',
            'cta_action.required' => 'Aksi tombol wajib dipilih.',
            'cta_action.in' => 'Aksi tombol tidak valid.',
            'cta_action_guest.in' => 'Aksi tombol untuk pengunjung tidak valid.',
        ];
    }

    public function openCreate(): void
    {
        if (RegistrationStep::count() >= 8) {
            session()->flash('error', 'Maksimal 8 tahap. Hapus atau gabungkan tahap sebelum menambah yang baru.');
            return;
        }

        $this->resetForm();
        $this->tab = 1;
        $this->sort_order = ((int) RegistrationStep::max('sort_order')) + 1;
        $this->showModal = true;
    }

    public function edit($id): void
    {
        $step = RegistrationStep::findOrFail($id);

        $this->editingId = $step->id;
        $this->short_title = $step->short_title;
        $this->short_sub = (string) $step->short_sub;
        $this->eyebrow = (string) $step->eyebrow;
        $this->title = $step->title;
        $this->description = (string) $step->description;
        $this->panel_title = (string) $step->panel_title;
        $this->tips_title = (string) $step->tips_title;
        $this->tips_body = (string) $step->tips_body;
        $this->footnote = (string) $step->footnote;
        $this->icon = $step->icon ?: 'shield';
        $this->cta_action = $step->cta_action ?: 'register';
        $this->cta_action_guest = (string) $step->cta_action_guest;
        $this->cta_label = (string) $step->cta_label;
        $this->cta_label_guest = (string) $step->cta_label_guest;
        $this->cta_label_admin = (string) $step->cta_label_admin;
        $this->cta_url = (string) $step->cta_url;
        $this->next_label = (string) $step->next_label;
        $this->sort_order = (int) $step->sort_order;
        $this->is_active = (bool) $step->is_active;

        $this->highlightsText = collect($step->highlights ?: [])
            ->map(function ($item) {
                $item = is_array($item) ? $item : ['title' => (string) $item, 'desc' => ''];
                $desc = trim((string) ($item['desc'] ?? ''));

                return $desc !== '' ? ($item['title'] ?? '') . ' | ' . $desc : (string) ($item['title'] ?? '');
            })
            ->implode("\n");

        $this->tab = 1;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        try {
            $validated = $this->validate();
        } catch (ValidationException $e) {
            // Buka tab yang berisi kolom bermasalah agar pesan error langsung terlihat.
            $this->tab = $this->tabForErrors(array_keys($e->errors()));
            throw $e;
        }

        $payload = [
            'short_title' => $validated['short_title'],
            'short_sub' => $validated['short_sub'] ?? null,
            'eyebrow' => $validated['eyebrow'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'highlights' => $this->parseHighlights($this->highlightsText),
            'panel_title' => $validated['panel_title'] ?? null,
            'tips_title' => $validated['tips_title'] ?? null,
            'tips_body' => $validated['tips_body'] ?? null,
            'footnote' => $validated['footnote'] ?? null,
            'icon' => $validated['icon'],
            'cta_action' => $validated['cta_action'],
            'cta_action_guest' => ($validated['cta_action_guest'] ?? null) ?: null,
            'cta_label' => ($validated['cta_label'] ?? null) ?: null,
            'cta_label_guest' => ($validated['cta_label_guest'] ?? null) ?: null,
            'cta_label_admin' => ($validated['cta_label_admin'] ?? null) ?: null,
            'cta_url' => $validated['cta_action'] === 'custom' ? ($validated['cta_url'] ?? null) : null,
            'next_label' => ($validated['next_label'] ?? null) ?: null,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => (bool) $this->is_active,
        ];

        if ($this->editingId) {
            $step = RegistrationStep::findOrFail($this->editingId);

            // Teks Indonesia yang berubah dikosongkan versi EN-nya agar diterjemahkan ulang otomatis.
            foreach (RegistrationStep::TRANSLATABLE_FIELDS as $field) {
                if ((string) ($payload[$field] ?? '') !== (string) ($step->{$field} ?? '')) {
                    $payload[$field . '_en'] = null;
                }
            }

            if ($payload['highlights'] !== ($step->highlights ?: [])) {
                $payload['highlights_en'] = null;
            }

            $step->update($payload);
            session()->flash('update', 'Tahap alur pendaftaran berhasil diperbarui.');
        } else {
            if ($payload['sort_order'] <= 0) {
                $payload['sort_order'] = ((int) RegistrationStep::max('sort_order')) + 1;
            }
            RegistrationStep::create($payload);
            session()->flash('create', 'Tahap alur pendaftaran berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function confirmDelete($id): void
    {
        if (RegistrationStep::count() <= 2) {
            session()->flash('error', 'Minimal harus ada 2 tahap pada alur pendaftaran.');
            return;
        }

        $step = RegistrationStep::find($id);
        if (!$step) {
            return;
        }

        $this->deletingId = $step->id;
        $this->deletingTitle = $step->short_title . ' — ' . $step->title;
    }

    public function delete(): void
    {
        if (!$this->deletingId) {
            return;
        }

        RegistrationStep::findOrFail($this->deletingId)->delete();
        $this->deletingId = null;
        $this->deletingTitle = '';

        session()->flash('delete', 'Tahap alur pendaftaran berhasil dihapus.');
    }

    public function toggleActive($id): void
    {
        $step = RegistrationStep::find($id);
        if (!$step) {
            return;
        }

        $step->is_active = !$step->is_active;
        $step->save();
    }

    public function moveUp($id): void
    {
        $this->move($id, 'up');
    }

    public function moveDown($id): void
    {
        $this->move($id, 'down');
    }

    private function move($id, $direction): void
    {
        $step = RegistrationStep::find($id);
        if (!$step) {
            return;
        }

        $neighbor = RegistrationStep::where('sort_order', $direction === 'up' ? '<' : '>', $step->sort_order)
            ->orderBy('sort_order', $direction === 'up' ? 'desc' : 'asc')
            ->orderBy('id', $direction === 'up' ? 'desc' : 'asc')
            ->first();

        if (!$neighbor) {
            return;
        }

        $currentOrder = $step->sort_order;
        $step->sort_order = $neighbor->sort_order;
        $neighbor->sort_order = $currentOrder;

        $step->save();
        $neighbor->save();
    }

    public function saveSection(): void
    {
        $this->validate([
            'section_title' => 'required|string|max:150',
            'section_subtitle' => 'nullable|string|max:500',
        ], [
            'section_title.required' => 'Judul section wajib diisi.',
            'section_title.max' => 'Judul section maksimal 150 karakter.',
            'section_subtitle.max' => 'Subjudul section maksimal 500 karakter.',
        ]);

        $previousTitle = (string) Setting::get('registration_flow_title', '');
        $previousSubtitle = (string) Setting::get('registration_flow_subtitle', '');

        Setting::set('registration_flow_title', $this->section_title, 'Judul section alur pendaftaran di beranda');
        Setting::set('registration_flow_subtitle', $this->section_subtitle, 'Subjudul section alur pendaftaran di beranda');

        // Perbarui terjemahan EN hanya saat teks Indonesia berubah, agar terjemahan lama tidak tertimpa.
        if ($this->section_title !== $previousTitle) {
            Setting::set('registration_flow_title_en', TranslationService::translate($this->section_title), 'Judul section alur pendaftaran di beranda (Bahasa Inggris)');
        }

        if ((string) $this->section_subtitle !== $previousSubtitle) {
            Setting::set('registration_flow_subtitle_en', TranslationService::translate($this->section_subtitle), 'Subjudul section alur pendaftaran di beranda (Bahasa Inggris)');
        }
        Setting::set('registration_flow_enabled', $this->section_enabled ? '1' : '0', 'Tampilkan section alur pendaftaran di beranda');

        RegistrationStep::clearCaches();

        session()->flash('section_saved', 'Pengaturan section berhasil disimpan.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->tab = 1;
        $this->short_title = '';
        $this->short_sub = '';
        $this->eyebrow = '';
        $this->title = '';
        $this->description = '';
        $this->highlightsText = '';
        $this->panel_title = '';
        $this->tips_title = '';
        $this->tips_body = '';
        $this->footnote = '';
        $this->icon = 'shield';
        $this->cta_action = 'register';
        $this->cta_action_guest = '';
        $this->cta_label = '';
        $this->cta_label_guest = '';
        $this->cta_label_admin = '';
        $this->cta_url = '';
        $this->next_label = '';
        $this->sort_order = 0;
        $this->is_active = true;
    }

    /**
     * Parse "Judul | Deskripsi" per line into structured highlights.
     */
    private function parseHighlights(?string $text): array
    {
        $lines = preg_split('/\r\n|\r|\n/', (string) $text);
        $items = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $parts = array_map('trim', explode('|', $line, 2));
            $title = $parts[0] !== '' ? $parts[0] : $line;

            $items[] = [
                'title' => mb_substr($title, 0, 120),
                'desc' => mb_substr($parts[1] ?? '', 0, 240),
            ];

            if (count($items) >= 6) {
                break;
            }
        }

        return $items;
    }

    public function render()
    {
        return view('livewire.admin.registration-flow.table', [
            'steps' => RegistrationStep::orderBy('sort_order')->orderBy('id')->get(),
            'tabErrors' => collect(array_keys(self::TAB_FIELDS))
                ->mapWithKeys(fn ($tab) => [$tab => $this->tabHasError($tab)])
                ->all(),
        ]);
    }
}
