<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use App\Services\TranslationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class HeroSetting extends Component
{
    use WithFileUploads;

    // Text fields (Indonesian & English)
    public string $title_prefix_id = '';
    public string $title_prefix_en = '';
    public string $title_highlight_id = '';
    public string $title_highlight_en = '';
    public string $description_id = '';
    public string $description_en = '';

    // Background Image fields
    public string $current_background_image = '';
    public string $custom_image_url = '';
    public $uploaded_image = null;

    // UI state
    public string $activeTab = 'id';
    public string $previewLocale = 'id';

    public function mount(): void
    {
        $this->loadSettings();
    }

    public function loadSettings(): void
    {
        $this->title_prefix_id = Setting::get('hero_title_prefix', 'Temukan Karir Impian,');
        $this->title_prefix_en = Setting::get('hero_title_prefix_en', 'Find Your Dream Career,');

        $this->title_highlight_id = Setting::get('hero_title_highlight', 'Wujudkan Potensi Terbaikmu');
        $this->title_highlight_en = Setting::get('hero_title_highlight_en', 'Realize Your Best Potential');

        $this->description_id = Setting::get('hero_description', 'Bergabunglah bersama ribuan profesional bertalenta. Jelajahi lowongan kerja, ikuti tes seleksi online terintegrasi, dan raih karir masa depan sekarang.');
        $this->description_en = Setting::get('hero_description_en', 'Join thousands of talented professionals. Explore job openings, take integrated online tests, and achieve your future career now.');

        $this->current_background_image = Setting::get('hero_background_image', 'https://i.pinimg.com/736x/20/dc/f0/20dcf0cfb0122a38d938e3c3011fb0d4.jpg');
        $this->custom_image_url = '';
        $this->uploaded_image = null;
    }

    public function getDisplayBackgroundImageProperty(): string
    {
        if ($this->uploaded_image) {
            try {
                return $this->uploaded_image->temporaryUrl();
            } catch (\Throwable $e) {
                // Fallback if temporaryUrl fails
            }
        }

        if (!empty(trim($this->custom_image_url))) {
            return trim($this->custom_image_url);
        }

        if (!empty($this->current_background_image)) {
            if (str_starts_with($this->current_background_image, 'http://') || str_starts_with($this->current_background_image, 'https://')) {
                return $this->current_background_image;
            }
            return asset('storage/' . $this->current_background_image);
        }

        return 'https://i.pinimg.com/736x/20/dc/f0/20dcf0cfb0122a38d938e3c3011fb0d4.jpg';
    }

    public function autoTranslate(): void
    {
        if (!empty(trim($this->title_prefix_id))) {
            $trans = TranslationService::translate($this->title_prefix_id, 'en', 'id');
            if ($trans) {
                $this->title_prefix_en = $trans;
            }
        }

        if (!empty(trim($this->title_highlight_id))) {
            $trans = TranslationService::translate($this->title_highlight_id, 'en', 'id');
            if ($trans) {
                $this->title_highlight_en = $trans;
            }
        }

        if (!empty(trim($this->description_id))) {
            $trans = TranslationService::translate($this->description_id, 'en', 'id');
            if ($trans) {
                $this->description_en = $trans;
            }
        }

        session()->flash('message', 'Teks berhasil diterjemahkan ke Bahasa Inggris secara otomatis!');
    }

    public function resetToDefaultTexts(): void
    {
        $this->title_prefix_id = 'Temukan Karir Impian,';
        $this->title_prefix_en = 'Find Your Dream Career,';
        $this->title_highlight_id = 'Wujudkan Potensi Terbaikmu';
        $this->title_highlight_en = 'Realize Your Best Potential';
        $this->description_id = 'Bergabunglah bersama ribuan profesional bertalenta. Jelajahi lowongan kerja, ikuti tes seleksi online terintegrasi, dan raih karir masa depan sekarang.';
        $this->description_en = 'Join thousands of talented professionals. Explore job openings, take integrated online tests, and achieve your future career now.';

        session()->flash('message', 'Teks Hero berhasil dikembalikan ke nilai default.');
    }

    public function resetToDefaultImage(): void
    {
        // Delete previously uploaded custom image from storage if it exists
        if (!empty($this->current_background_image) && !str_starts_with($this->current_background_image, 'http')) {
            Storage::disk('public')->delete($this->current_background_image);
        }

        $defaultImg = 'https://i.pinimg.com/736x/20/dc/f0/20dcf0cfb0122a38d938e3c3011fb0d4.jpg';
        Setting::set('hero_background_image', $defaultImg, 'URL atau path gambar background Hero Banner Beranda');

        $this->current_background_image = $defaultImg;
        $this->custom_image_url = '';
        $this->uploaded_image = null;

        $this->clearAllHeroCache();

        session()->flash('message', 'Gambar latar Hero berhasil direset ke gambar bawaan!');
    }

    public function removeUploadedPreview(): void
    {
        $this->uploaded_image = null;
    }

    public function save(): void
    {
        $this->validate([
            'title_prefix_id' => 'required|string|max:255',
            'title_prefix_en' => 'nullable|string|max:255',
            'title_highlight_id' => 'required|string|max:255',
            'title_highlight_en' => 'nullable|string|max:255',
            'description_id' => 'required|string|max:1000',
            'description_en' => 'nullable|string|max:1000',
            'uploaded_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'custom_image_url' => 'nullable|url|max:1000',
        ], [
            'title_prefix_id.required' => 'Judul baris pertama (Indonesia) wajib diisi.',
            'title_highlight_id.required' => 'Teks highlight hijau neon (Indonesia) wajib diisi.',
            'description_id.required' => 'Deskripsi / slogan (Indonesia) wajib diisi.',
            'uploaded_image.image' => 'File yang diunggah harus berupa gambar valid.',
            'uploaded_image.max' => 'Ukuran gambar maksimal 5 MB.',
            'custom_image_url.url' => 'Format tautan URL gambar tidak valid.',
        ]);

        // Handle Image Update
        if ($this->uploaded_image) {
            // Delete old uploaded image if stored locally
            if (!empty($this->current_background_image) && !str_starts_with($this->current_background_image, 'http')) {
                Storage::disk('public')->delete($this->current_background_image);
            }

            $path = $this->uploaded_image->store('hero-banners', 'public');
            Setting::set('hero_background_image', $path, 'URL atau path gambar background Hero Banner Beranda');
            $this->current_background_image = $path;
            $this->uploaded_image = null;
            $this->custom_image_url = '';
        } elseif (!empty(trim($this->custom_image_url))) {
            // If user provided custom direct URL
            if (!empty($this->current_background_image) && !str_starts_with($this->current_background_image, 'http')) {
                Storage::disk('public')->delete($this->current_background_image);
            }

            Setting::set('hero_background_image', trim($this->custom_image_url), 'URL atau path gambar background Hero Banner Beranda');
            $this->current_background_image = trim($this->custom_image_url);
            $this->custom_image_url = '';
        }

        // Save Text Fields
        Setting::set('hero_title_prefix', trim($this->title_prefix_id), 'Teks judul baris pertama pada Hero Banner Beranda (Bahasa Indonesia)');
        Setting::set('hero_title_prefix_en', trim($this->title_prefix_en ?: $this->title_prefix_id), 'Teks judul baris pertama pada Hero Banner Beranda (Bahasa Inggris)');

        Setting::set('hero_title_highlight', trim($this->title_highlight_id), 'Teks highlight hijau neon baris kedua pada Hero Banner Beranda (Bahasa Indonesia)');
        Setting::set('hero_title_highlight_en', trim($this->title_highlight_en ?: $this->title_highlight_id), 'Teks highlight hijau neon baris kedua pada Hero Banner Beranda (Bahasa Inggris)');

        Setting::set('hero_description', trim($this->description_id), 'Teks deskripsi / slogan di bawah headline Hero Banner Beranda (Bahasa Indonesia)');
        Setting::set('hero_description_en', trim($this->description_en ?: $this->description_id), 'Teks deskripsi / slogan di bawah headline Hero Banner Beranda (Bahasa Inggris)');

        $this->clearAllHeroCache();

        session()->flash('message', 'Pengaturan Hero Banner Beranda berhasil diperbarui!');
    }

    protected function clearAllHeroCache(): void
    {
        Cache::forget('frontend_hero_settings_id');
        Cache::forget('frontend_hero_settings_en');
        Cache::forget('app_setting_hero_title_prefix');
        Cache::forget('app_setting_hero_title_prefix_en');
        Cache::forget('app_setting_hero_title_highlight');
        Cache::forget('app_setting_hero_title_highlight_en');
        Cache::forget('app_setting_hero_description');
        Cache::forget('app_setting_hero_description_en');
        Cache::forget('app_setting_hero_background_image');
    }

    public function render()
    {
        return view('livewire.admin.hero.setting');
    }
}

