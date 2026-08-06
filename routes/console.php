<?php

use App\Jobs\SendFollowUpReminderJob;
use App\Models\LeadFollowUp;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Fires exactly one reminder email per due follow-up (reminder_sent guards against
// re-sending on the next run — including against a still-queued duplicate dispatch,
// see App\Jobs\SendFollowUpReminderJob) — see App\Jobs\SendFollowUpReminderJob.
// Requires a server cron running `schedule:run` (see the queue:work entry below for
// why that alone is now enough — no separate `queue:work`/`schedule:work` daemon).
Schedule::call(function () {
    LeadFollowUp::where('status', 'pending')
        ->where('reminder_sent', false)
        ->where('due_at', '<=', now())
        ->chunkById(100, function ($followUps) {
            foreach ($followUps as $followUp) {
                SendFollowUpReminderJob::dispatch($followUp);
            }
        });
})->everyFiveMinutes()->name('lead-follow-up-reminders')->withoutOverlapping();

// Shared-hosting-safe substitute for a persistent `queue:work` daemon (Hostinger and
// similar shared plans kill long-running background processes, and don't offer
// Supervisor). --stop-when-empty makes the worker exit as soon as it drains the
// queue instead of idling forever, so each cron tick is a short-lived PHP process —
// exactly what shared hosting expects. withoutOverlapping() stops a new tick from
// starting a second worker while a previous, slow-draining one is still running.
// Runs AFTER the reminder dispatch above so reminders queued this same tick are
// drained immediately rather than waiting for the next one.
Schedule::command('queue:work --stop-when-empty --tries=3 --timeout=60')
    ->everyMinute()
    ->withoutOverlapping()
    ->name('queue-worker');
