<?php

namespace App\Mail;

use App\Models\EmployeeProfile;
use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmployeePromotedMail extends Mailable
{
    use Queueable, SerializesModels;

    public JobApplication $application;
    public EmployeeProfile $employeeProfile;

    /**
     * Create a new message instance.
     */
    public function __construct(JobApplication $application, EmployeeProfile $employeeProfile)
    {
        $this->application = $application;
        $this->employeeProfile = $employeeProfile;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $jobTitle = $this->employeeProfile->position_title ?? ($this->application->job?->title ?? 'Karyawan');
        $companyName = $this->employeeProfile->company?->name
            ?? ($this->application->job?->company?->name ?? config('app.name', 'MIKA CAREER'));

        return new Envelope(
            subject: "Selamat Bergabung & Aktivasi Portal Karyawan: {$jobTitle} - {$companyName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.employee_promoted',
            text: 'emails.employee_promoted_plain',
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
