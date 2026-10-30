<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class JobRecommendationMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public Collection $matchedJobs;
    public string $period;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Collection $matchedJobs, string $period = 'Harian')
    {
        $this->user = $user;
        $this->matchedJobs = $matchedJobs;
        $this->period = $period;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $appName = config('app.name', 'MIKA CAREER');

        return new Envelope(
            subject: "Rekomendasi Lowongan Kerja ({$this->period}) - {$appName}",
        );
    }

    /**
     * Get the message headers.
     */
    public function headers(): Headers
    {
        $unsubscribeUrl = route('profile', ['tab' => 'pengalaman']);

        return new Headers(
            text: [
                'List-Unsubscribe' => "<{$unsubscribeUrl}>",
                'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.job_recommendation',
            text: 'emails.job_recommendation_plain',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
