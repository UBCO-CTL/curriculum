<?php

namespace App\Mail;

use App\Models\CourseCloneRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseCloneApprovalRequestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $programName;

    public string $courseLabel;

    public string $requesterName;

    public string $approveUrl;

    public string $denyUrl;

    public function __construct(public readonly CourseCloneRequest $cloneRequest, public readonly User $recipient, string $approveUrl, string $denyUrl)
    {
        $this->cloneRequest->loadMissing(['program', 'originalCourse', 'requestedBy']);

        $course = $this->cloneRequest->originalCourse;
        $program = $this->cloneRequest->program;

        $this->programName = $program?->program ?? 'Program';
        $this->courseLabel = trim(implode(' ', array_filter([
            $course?->course_code,
            $course?->course_num,
            $course?->course_title,
        ])));
        $this->requesterName = $this->cloneRequest->requestedBy?->name ?? 'A collaborator';
        $this->approveUrl = $approveUrl;
        $this->denyUrl = $denyUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Clone approval requested: '.$this->courseLabel,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.course_clone_approval',
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
