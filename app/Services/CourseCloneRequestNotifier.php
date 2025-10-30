<?php

namespace App\Services;

use App\Mail\CourseCloneApprovalRequestMail;
use App\Models\CourseCloneRequest;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class CourseCloneRequestNotifier
{
    public function __construct(private readonly int $expiryDays = 14)
    {
    }

    /**
     * @param  array<int,array{0: CourseCloneRequest, 1: string}>  $payloads
     */
    public function notifyMany(array $payloads): void
    {
        foreach ($payloads as $payload) {
            $this->notify($payload[0], $payload[1]);
        }
    }

    public function notify(CourseCloneRequest $cloneRequest, string $token): void
    {
        $cloneRequest->loadMissing(['program', 'originalCourse.owners', 'requestedBy']);

        $owners = $cloneRequest->originalCourse?->owners ?? new Collection();

        if ($owners->isEmpty()) {
            return;
        }

        $expiry = $cloneRequest->expires_at ?? now()->addDays($this->expiryDays);

        foreach ($owners->unique('id') as $owner) {
            $approveUrl = $this->signedUrl('courseCloneRequests.approve', $cloneRequest, $owner, $token, $expiry);
            $denyUrl = $this->signedUrl('courseCloneRequests.deny', $cloneRequest, $owner, $token, $expiry);

            Mail::to($owner->email)->queue(
                new CourseCloneApprovalRequestMail($cloneRequest, $owner, $approveUrl, $denyUrl)
            );
        }
    }

    private function signedUrl(string $routeName, CourseCloneRequest $cloneRequest, User $owner, string $token, \DateTimeInterface $expiry): string
    {
        return URL::temporarySignedRoute($routeName, $expiry, [
            'courseCloneRequest' => $cloneRequest->id,
            'approver' => $owner->id,
            'token' => $token,
        ]);
    }
}

