<?php

namespace App\Livewire\Applicant;

use App\Models\ApplicantProfile;
use App\Models\SocialMedia as SocialMediaModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SocialMedia extends Component
{
    public $linkedin;
    public $instagram;
    public $twitter;
    public $tiktok;
    public $facebook;
    public $github;

    public function mount()
    {
        $user = Auth::user();
        $profile = ApplicantProfile::where('user_id', $user->id)->first();

        if ($profile) {
            $socials = SocialMediaModel::where('profile_id', $profile->id)
                ->pluck('url', 'platform_name')
                ->toArray();

            $this->linkedin = $socials['LinkedIn'] ?? '';
            $this->instagram = $socials['Instagram'] ?? '';
            $this->twitter = $socials['Twitter / X'] ?? ($socials['Twitter'] ?? '');
            $this->tiktok = $socials['TikTok'] ?? '';
            $this->facebook = $socials['Facebook'] ?? '';
            $this->github = $socials['GitHub / Portofolio'] ?? ($socials['GitHub'] ?? '');
        }
    }

    public function save()
    {
        $this->validate([
            'linkedin' => 'nullable|url|max:500',
            'instagram' => 'nullable|url|max:500',
            'twitter' => 'nullable|url|max:500',
            'tiktok' => 'nullable|url|max:500',
            'facebook' => 'nullable|url|max:500',
            'github' => 'nullable|url|max:500',
        ], [
            'linkedin.url' => 'Format URL LinkedIn tidak valid (contoh: https://linkedin.com/in/username).',
            'instagram.url' => 'Format URL Instagram tidak valid (contoh: https://instagram.com/username).',
            'twitter.url' => 'Format URL Twitter / X tidak valid (contoh: https://x.com/username).',
            'tiktok.url' => 'Format URL TikTok tidak valid (contoh: https://tiktok.com/@username).',
            'facebook.url' => 'Format URL Facebook tidak valid (contoh: https://facebook.com/username).',
            'github.url' => 'Format URL GitHub / Portofolio tidak valid (contoh: https://github.com/username).',
        ]);

        $user = Auth::user();
        $profile = ApplicantProfile::firstOrCreate(['user_id' => $user->id]);

        $platforms = [
            'LinkedIn' => trim($this->linkedin),
            'Instagram' => trim($this->instagram),
            'Twitter / X' => trim($this->twitter),
            'TikTok' => trim($this->tiktok),
            'Facebook' => trim($this->facebook),
            'GitHub / Portofolio' => trim($this->github),
        ];

        foreach ($platforms as $name => $url) {
            if (!empty($url)) {
                SocialMediaModel::where('profile_id', $profile->id)
                    ->where('platform_name', $name)
                    ->delete();

                SocialMediaModel::create([
                    'profile_id' => $profile->id,
                    'platform_name' => $name,
                    'url' => $url,
                ]);
            } else {
                SocialMediaModel::where('profile_id', $profile->id)
                    ->where('platform_name', $name)
                    ->delete();
            }
        }

        $this->dispatch('profile-updated', message: __('Tautan media sosial berhasil diperbarui.'), action: 'update');
    }

    public function render()
    {
        return view('livewire.applicant.social_media');
    }
}
