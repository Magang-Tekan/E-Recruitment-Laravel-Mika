<?php

namespace App\Livewire\Frontend;

use App\Services\ChatbotService;
use Livewire\Component;

class ChatbotWidget extends Component
{
    public bool $isOpen = false;
    public string $input = '';
    public array $messages = [];
    public bool $isTyping = false;

    public array $quickPills = [];

    public function mount(): void
    {
        $isEn = app()->getLocale() === 'en';

        $this->quickPills = $isEn ? [
            'What jobs are open?',
            'How do I apply?',
            'Recruitment selection stages',
            'My application status',
        ] : [
            'Lowongan apa yang buka?',
            'Bagaimana cara melamar?',
            'Tahapan seleksi rekrutmen',
            'Status lamaran saya',
        ];

        $this->messages = [
            [
                'role' => 'assistant',
                'text' => $isEn
                    ? 'Hello! I am Mika Assistant. I am here to help you with job vacancies, requirements, or the application process at Mika Career. How can I assist you today?'
                    : 'Halo! Saya Mika Assistant. Siap membantu Anda seputar informasi lowongan kerja, kualifikasi, atau alur pendaftaran di Mika Career. Ada yang bisa saya bantu?',
                'time' => now()->format('H:i'),
            ]
        ];
    }

    public function toggle(): void
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen) {
            $this->dispatch('chatbot-opened');
        }
    }

    public function askQuick(string $question): void
    {
        $this->input = $question;
        $this->sendMessage();
    }

    public function sendMessage(): void
    {
        $service = app(ChatbotService::class);
        $userText = trim($this->input);
        if (empty($userText)) {
            return;
        }

        // Add user message
        $this->messages[] = [
            'role' => 'user',
            'text' => $userText,
            'time' => now()->format('H:i'),
        ];

        $this->input = '';
        $this->dispatch('chatbot-scroll-bottom');

        // Fetch AI/Service response
        $reply = $service->reply($userText, $this->messages);

        $this->messages[] = [
            'role' => 'assistant',
            'text' => $reply,
            'time' => now()->format('H:i'),
        ];

        $this->dispatch('chatbot-scroll-bottom');
    }

    public function clearMessages(): void
    {
        $this->mount();
        $this->dispatch('chatbot-scroll-bottom');
    }

    public function render()
    {
        $mika = \Illuminate\Support\Facades\Cache::remember('frontend_main_company', 86400, function () {
            return \App\Models\Company::where('name', 'like', '%Mitra Karya Analitika%')
                ->orWhere('name', 'like', '%MIKA%')
                ->first() ?? \App\Models\Company::first();
        });

        $waPhone = $mika?->whatsapp_phone ?? '6281770555554';
        $rawPhone = $mika?->phone ?? '0817 7055 5554';
        $companyName = $mika?->name ?? 'PT Mitra Karya Analitika';
        $waText = urlencode("Halo Tim HR {$companyName}, saya ingin bertanya seputar rekrutmen dan lowongan kerja.");
        $waUrl = "https://wa.me/{$waPhone}?text={$waText}";

        return view('livewire.frontend.chatbot-widget', [
            'mika' => $mika,
            'waPhone' => $waPhone,
            'rawPhone' => $rawPhone,
            'waUrl' => $waUrl,
        ]);
    }
}

