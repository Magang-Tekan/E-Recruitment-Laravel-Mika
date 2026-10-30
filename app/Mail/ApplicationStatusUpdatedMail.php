<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public JobApplication $application;
    public string $status;
    public ?string $notes;

    /**
     * Create a new message instance.
     */
    public function __construct(JobApplication $application, string $status, ?string $notes = null)
    {
        $this->application = $application;
        $this->status = $status;
        $this->notes = $notes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $jobTitle = $this->application->job?->title ?? 'Lamaran Pekerjaan';
        $companyName = $this->application->job?->company?->name ?? config('app.name', 'MIKA CAREER');

        $statusSubjects = [
            'Reviewed'         => "Hasil Seleksi Berkas: {$jobTitle} - {$companyName}",
            'Partial Approved' => "Pembaruan Seleksi Tahap Awal: {$jobTitle} - {$companyName}",
            'Shortlisted'      => "Informasi Tahap Seleksi: {$jobTitle} - {$companyName}",
            'Interview'        => "Undangan Wawancara Kerja: {$jobTitle} - {$companyName}",
            'Accepted'         => "Pemberitahuan Penerimaan Kerja: {$jobTitle} - {$companyName}",
            'Rejected'         => "Pemberitahuan Hasil Seleksi Lamaran: {$jobTitle} - {$companyName}",
            'Submitted'        => "Konfirmasi Penerimaan Lamaran: {$jobTitle} - {$companyName}",
        ];

        $subject = $statusSubjects[$this->status] ?? "Pembaruan Status Lamaran: {$jobTitle} - {$companyName}";

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.application_status_updated',
            text: 'emails.application_status_updated_plain',
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
