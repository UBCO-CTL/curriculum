<?php

namespace App\Console\Commands;

use App\Models\CourseCloneRequest;
use Illuminate\Console\Command;

class ExpireCourseCloneRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expire-course-clone-requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark course clone requests as expired when they pass their expiry date.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredCount = 0;

        CourseCloneRequest::where('status', CourseCloneRequest::STATUS_PENDING)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->chunkById(100, function ($requests) use (&$expiredCount) {
                foreach ($requests as $request) {
                    $request->markExpired();
                    $expiredCount++;
                }
            });

        $this->info("Expired {$expiredCount} course clone request(s).");
    }
}
