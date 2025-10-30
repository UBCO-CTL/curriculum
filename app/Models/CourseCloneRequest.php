<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class CourseCloneRequest extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_DENIED = 'denied';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_FAILED = 'failed';

    protected $guarded = ['id'];

    protected $casts = [
        'approved_at' => 'datetime',
        'responded_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_notified_at' => 'datetime',
        'course_required' => 'integer',
        'instructor_assigned' => 'integer',
        'map_status' => 'integer',
        'original_program_id' => 'integer',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    public function originalProgram(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'original_program_id', 'program_id');
    }

    public function originalCourse(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'original_course_id', 'course_id');
    }

    public function clonedCourse(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'cloned_course_id', 'course_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by_user_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function markApproved(User $respondedBy, ?Carbon $timestamp = null): void
    {
        $timestamp ??= now();

        $this->forceFill([
            'status' => self::STATUS_APPROVED,
            'responded_by_user_id' => $respondedBy->getKey(),
            'responded_at' => $timestamp,
            'approved_at' => $timestamp,
        ])->save();
    }

    public function markDenied(User $respondedBy, ?Carbon $timestamp = null): void
    {
        $timestamp ??= now();

        $this->forceFill([
            'status' => self::STATUS_DENIED,
            'responded_by_user_id' => $respondedBy->getKey(),
            'responded_at' => $timestamp,
            'approved_at' => null,
        ])->save();
    }

    public function markCancelled(User $respondedBy, ?Carbon $timestamp = null): void
    {
        $timestamp ??= now();

        $this->forceFill([
            'status' => self::STATUS_CANCELLED,
            'responded_by_user_id' => $respondedBy->getKey(),
            'responded_at' => $timestamp,
            'approved_at' => null,
        ])->save();
    }

    public function markExpired(?Carbon $timestamp = null): void
    {
        $timestamp ??= now();

        $this->forceFill([
            'status' => self::STATUS_EXPIRED,
            'responded_by_user_id' => null,
            'responded_at' => $timestamp,
            'approved_at' => null,
        ])->save();
    }

    public function markFailed(?Carbon $timestamp = null): void
    {
        $timestamp ??= now();

        $this->forceFill([
            'status' => self::STATUS_FAILED,
            'responded_at' => $timestamp,
        ])->save();
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function matchesToken(?string $token): bool
    {
        if ($token === null || $this->token_hash === null) {
            return false;
        }

        return hash_equals($this->token_hash, hash('sha256', $token));
    }

    public function issueToken(int $expiryDays): string
    {
        $token = Str::random(64);

        $this->forceFill([
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addDays($expiryDays),
            'last_notified_at' => now(),
            'status' => self::STATUS_PENDING,
            'responded_by_user_id' => null,
            'responded_at' => null,
            'approved_at' => null,
        ])->save();

        return $token;
    }
}
