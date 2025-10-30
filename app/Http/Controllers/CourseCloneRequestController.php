<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCourseClone;
use App\Models\CourseCloneRequest;
use App\Models\CourseUser;
use App\Models\ProgramUser;
use App\Models\User;
use App\Services\CourseCloneRequestNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CourseCloneRequestController extends Controller
{
    private const EXPIRY_DAYS = 14;

    public function __construct(private readonly CourseCloneRequestNotifier $cloneRequestNotifier)
    {
    }

    public function approve(Request $request, CourseCloneRequest $courseCloneRequest): View
    {
        $approver = $this->resolveApprover($request, $courseCloneRequest);

        if ($approver === null) {
            return $this->renderResult('Invalid Approval Link', 'We could not verify this approval link.');
        }

        if (! $courseCloneRequest->matchesToken($request->query('token'))) {
            return $this->renderResult('Invalid Approval Link', 'This approval link is invalid or has already been used.');
        }

        if ($courseCloneRequest->isExpired()) {
            $courseCloneRequest->markExpired();

            return $this->renderResult('Request Expired', 'This clone request has expired. The requester will need to send a new approval link.');
        }

        if (! $courseCloneRequest->isPending()) {
            return $this->renderResult('Already Processed', 'This clone request has already been processed. No further action is required.');
        }

        DB::transaction(function () use ($courseCloneRequest, $approver) {
            $courseCloneRequest->markApproved($approver);

            ProcessCourseClone::dispatch($courseCloneRequest->id)->afterCommit();
        });

        return $this->renderResult('Clone Approved', 'Thanks! We will clone the course and update the program automatically.');
    }

    public function deny(Request $request, CourseCloneRequest $courseCloneRequest): View
    {
        $approver = $this->resolveApprover($request, $courseCloneRequest);

        if ($approver === null) {
            return $this->renderResult('Invalid Denial Link', 'We could not verify this denial link.');
        }

        if (! $courseCloneRequest->matchesToken($request->query('token'))) {
            return $this->renderResult('Invalid Denial Link', 'This denial link is invalid or has already been used.');
        }

        if ($courseCloneRequest->isExpired()) {
            $courseCloneRequest->markExpired();

            return $this->renderResult('Request Expired', 'This clone request has expired. The requester will need to send a new approval link.');
        }

        if (! $courseCloneRequest->isPending()) {
            return $this->renderResult('Already Processed', 'This clone request has already been processed. No further action is required.');
        }

        $courseCloneRequest->markDenied($approver);

        return $this->renderResult('Clone Request Denied', 'We have recorded your decision. The requester has been notified.');
    }

    public function resend(Request $request, CourseCloneRequest $courseCloneRequest): RedirectResponse
    {
        $user = Auth::user();

        if ($user === null || ! $this->canManageRequest($courseCloneRequest, $user)) {
            abort(403);
        }

        if (! $courseCloneRequest->isPending() && ! $courseCloneRequest->isExpired()) {
            return redirect()->back()->with('warning', 'Clone request has already been processed and cannot be resent.');
        }

        $token = $courseCloneRequest->issueToken(self::EXPIRY_DAYS);

        $this->cloneRequestNotifier->notify($courseCloneRequest, $token);

        return redirect()->back()->with('success', 'Clone request notification has been resent.');
    }
    public function cancel(Request $request, CourseCloneRequest $courseCloneRequest): RedirectResponse
    {
        $user = Auth::user();

        if ($user === null || ! $this->canManageRequest($courseCloneRequest, $user)) {
            abort(403);
        }

        if (! $courseCloneRequest->isPending() && ! $courseCloneRequest->isExpired()) {
            return redirect()->back()->with('warning', 'Only pending or expired clone requests can be cancelled.');
        }
        $courseCloneRequest->markCancelled($user);

        return redirect()->back()->with('success', 'Clone request has been cancelled.');
    }

    private function renderResult(string $title, string $message): View
    {
        return view('course_clone_requests.result', compact('title', 'message'));
    }

    private function resolveApprover(Request $request, CourseCloneRequest $courseCloneRequest): ?User
    {
        $approverId = $request->query('approver');

        if (! is_numeric($approverId)) {
            return null;
        }

        $approver = User::find((int) $approverId);

        if ($approver === null) {
            return null;
        }

        $isOwner = CourseUser::where('course_id', $courseCloneRequest->original_course_id)
            ->where('user_id', $approver->id)
            ->where('permission', 1)
            ->exists();

        return $isOwner ? $approver : null;
    }

    private function canManageRequest(CourseCloneRequest $courseCloneRequest, User $user): bool
    {
        if ($courseCloneRequest->requested_by_user_id === $user->id) {
            return true;
        }

        return ProgramUser::where('program_id', $courseCloneRequest->program_id)
            ->where('user_id', $user->id)
            ->where('permission', 1)
            ->exists();
    }
}

