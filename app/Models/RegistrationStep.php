<?php

namespace App\Models;

use App\Services\TranslationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RegistrationStep extends Model
{
    protected $fillable = [
        'sort_order',
        'short_title',
        'short_title_en',
        'short_sub',
        'short_sub_en',
        'eyebrow',
        'eyebrow_en',
        'title',
        'title_en',
        'description',
        'description_en',
        'highlights',
        'highlights_en',
        'panel_title',
        'panel_title_en',
        'tips_title',
        'tips_title_en',
        'tips_body',
        'tips_body_en',
        'footnote',
        'footnote_en',
        'icon',
        'cta_action',
        'cta_action_guest',
        'cta_label',
        'cta_label_en',
        'cta_label_guest',
        'cta_label_guest_en',
        'cta_label_admin',
        'cta_label_admin_en',
        'cta_url',
        'next_label',
        'next_label_en',
        'is_active',
    ];

    protected $casts = [
        'highlights' => 'array',
        'highlights_en' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Ikon yang didukung panel "Ketentuan Tahap".
     */
    public const ICONS = ['shield', 'document', 'briefcase', 'clipboard', 'video', 'award'];

    /**
     * Aksi tombol yang didukung.
     */
    public const CTA_ACTIONS = ['register', 'login', 'profile', 'profile_history', 'jobs', 'custom'];

    /**
     * Kolom teks berbahasa Indonesia yang otomatis diterjemahkan ke Inggris.
     */
    public const TRANSLATABLE_FIELDS = [
        'short_title',
        'short_sub',
        'eyebrow',
        'title',
        'description',
        'panel_title',
        'tips_title',
        'tips_body',
        'footnote',
        'cta_label',
        'cta_label_guest',
        'cta_label_admin',
        'next_label',
    ];

    /**
     * Set false saat seeding agar tidak memanggil API terjemahan untuk data contoh
     * (teks contoh sudah tersedia di lang/en.json).
     */
    public static bool $autoTranslate = true;

    public static function withoutAutoTranslate(callable $callback): mixed
    {
        $previous = static::$autoTranslate;
        static::$autoTranslate = false;

        try {
            return $callback();
        } finally {
            static::$autoTranslate = $previous;
        }
    }

    protected static function booted(): void
    {
        static::saving(function (self $step) {
            if (static::$autoTranslate) {
                $step->fillMissingTranslations();
            }
        });

        static::saved(function () {
            self::clearCaches();
        });

        static::deleted(function () {
            self::clearCaches();
        });
    }

    /**
     * Terjemahkan kolom Inggris yang masih kosong (mis. setelah admin mengubah teks ID).
     */
    protected function fillMissingTranslations(): void
    {
        foreach (self::TRANSLATABLE_FIELDS as $field) {
            $englishField = $field . '_en';

            if (blank($this->{$englishField}) && filled($this->{$field})) {
                $this->{$englishField} = TranslationService::translate($this->{$field});
            }
        }

        if (blank($this->highlights_en) && !empty($this->highlights)) {
            $this->highlights_en = self::translateHighlights($this->highlights);
        }
    }

    public static function translateHighlights(array $highlights): array
    {
        $translated = [];

        foreach ($highlights as $item) {
            $title = is_array($item) ? (string) ($item['title'] ?? '') : (string) $item;
            $desc = is_array($item) ? (string) ($item['desc'] ?? '') : '';

            $translated[] = [
                'title' => $title !== '' ? (string) TranslationService::translate($title) : '',
                'desc' => $desc !== '' ? (string) TranslationService::translate($desc) : '',
            ];
        }

        return $translated;
    }

    public static function clearCaches(): void
    {
        foreach (['id', 'en'] as $locale) {
            Cache::forget('frontend_registration_steps_' . $locale);
        }
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Ambil nilai sesuai locale aktif: kolom _en untuk EN, selain itu teks ID
     * (dibungkus __() agar terjemahan lama di lang/en.json tetap terpakai).
     */
    protected function localizedValue(string $field): ?string
    {
        $english = $this->{$field . '_en'};

        if (app()->getLocale() === 'en' && filled($english) && !str_contains((string) $english, 'QUERY LENGTH LIMIT')) {
            return (string) $english;
        }

        $value = $this->{$field};

        return $value !== null && $value !== '' ? __((string) $value) : null;
    }

    public function getLocalizedShortTitleAttribute(): string
    {
        return (string) $this->localizedValue('short_title');
    }

    public function getLocalizedShortSubAttribute(): ?string
    {
        return $this->localizedValue('short_sub');
    }

    public function getLocalizedEyebrowAttribute(): ?string
    {
        return $this->localizedValue('eyebrow');
    }

    public function getLocalizedTitleAttribute(): string
    {
        return (string) $this->localizedValue('title');
    }

    public function getLocalizedDescriptionAttribute(): ?string
    {
        return $this->localizedValue('description');
    }

    public function getLocalizedPanelTitleAttribute(): ?string
    {
        return $this->localizedValue('panel_title');
    }

    public function getLocalizedTipsTitleAttribute(): ?string
    {
        return $this->localizedValue('tips_title');
    }

    public function getLocalizedTipsBodyAttribute(): ?string
    {
        return $this->localizedValue('tips_body');
    }

    public function getLocalizedFootnoteAttribute(): ?string
    {
        return $this->localizedValue('footnote');
    }

    public function getLocalizedCtaLabelAttribute(): ?string
    {
        return $this->localizedValue('cta_label');
    }

    public function getLocalizedCtaLabelGuestAttribute(): ?string
    {
        return $this->localizedValue('cta_label_guest');
    }

    public function getLocalizedCtaLabelAdminAttribute(): ?string
    {
        return $this->localizedValue('cta_label_admin');
    }

    public function getLocalizedNextLabelAttribute(): ?string
    {
        return $this->localizedValue('next_label');
    }

    /**
     * Baris eyebrow "Tahap 01 — ..." sesuai locale aktif.
     */
    public function localizedEyebrowLine(int $number): string
    {
        $number = str_pad((string) $number, 2, '0', STR_PAD_LEFT);
        $rawEyebrow = $this->eyebrow ?: $this->short_title;

        if (app()->getLocale() === 'en' && (filled($this->eyebrow_en) || filled($this->short_title_en))) {
            return __('Tahap') . ' ' . $number . ' — ' . ($this->localized_eyebrow ?: $this->localized_short_title);
        }

        return __('Tahap ' . $number . ' — ' . $rawEyebrow);
    }

    /**
     * Judul panel kanan; default "Ketentuan Tahap 01" mengikuti locale.
     */
    public function localizedPanelHeading(int $number): string
    {
        if ($this->localized_panel_title) {
            return $this->localized_panel_title;
        }

        return __('Ketentuan Tahap ' . str_pad((string) $number, 2, '0', STR_PAD_LEFT));
    }

    public function getLocalizedHighlightsAttribute(): array
    {
        $items = is_array($this->highlights) ? $this->highlights : [];

        if (app()->getLocale() === 'en' && !empty($this->highlights_en) && is_array($this->highlights_en)) {
            $items = $this->highlights_en;
        }

        return array_map(function ($item) {
            $title = is_array($item) ? (string) ($item['title'] ?? '') : (string) $item;
            $desc = is_array($item) ? (string) ($item['desc'] ?? '') : '';

            return [
                'title' => $title !== '' ? __($title) : '',
                'desc' => $desc !== '' ? __($desc) : '',
            ];
        }, $items);
    }

    /**
     * Resolve tombol aksi sesuai status login & role pengunjung.
     */
    public function getCtaAttribute(): array
    {
        $user = auth()->user();
        $isAdmin = false;
        $isRecruiter = false;

        if ($user) {
            $roleName = strtolower($user->role?->name ?? '');
            $isRecruiter = $user->role_id == 2 || $roleName === 'recruiter' || (bool) $user->is_recruiter;
            $isAdmin = $user->role_id == 1
                || in_array($roleName, ['admin', 'superadmin', 'recruiter'], true)
                || (bool) $user->is_recruiter;
        }

        $action = $user
            ? ($this->cta_action ?: 'profile')
            : ($this->cta_action_guest ?: ($this->cta_action ?: 'register'));

        $label = $user
            ? ($isAdmin && $this->localized_cta_label_admin ? $this->localized_cta_label_admin : $this->localized_cta_label)
            : ($this->localized_cta_label_guest ?: $this->localized_cta_label);

        $url = match ($action) {
            'register' => $user
                ? ($isAdmin
                    ? ($isRecruiter ? route('recruiter.dashboard') : route('admin.dashboard'))
                    : route('profile'))
                : route('register'),
            'login', 'profile' => $user ? route('profile') : route('login'),
            'profile_history' => $user ? route('profile', ['tab' => 'riwayat']) : route('login'),
            'jobs' => route('jobs.index'),
            'custom' => $this->normalizeUrl($this->cta_url),
            default => $user ? route('profile') : route('login'),
        };

        return [
            'action' => $action,
            'url' => $url,
            'label' => $label ?: __('Selengkapnya'),
            'arrow' => !$user && $action === 'register',
        ];
    }

    private function normalizeUrl(?string $url): string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return route('home') . '#alur-pendaftaran';
        }

        if (!Str::startsWith($url, ['http://', 'https://', '/', '#', 'mailto:', 'tel:'])) {
            $url = 'https://' . $url;
        }

        return $url;
    }
}
