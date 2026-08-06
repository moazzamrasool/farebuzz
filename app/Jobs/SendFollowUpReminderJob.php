<?php

namespace App\Jobs;

use App\Mail\FollowUpReminderMail;
use App\Models\LeadFollowUp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendFollowUpReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public LeadFollowUp $followUp)
    {
    }

    public function handle(): void
    {
        $followUp = $this->followUp->fresh();

        // Follow-up may have been completed/deleted between dispatch and execution —
        // and, since the scheduler re-queries every tick without knowing what's still
        // sitting unprocessed in the queue, a slow/backed-up worker can let the same
        // due follow-up get dispatched twice before the first job finishes. The
        // reminder_sent check is what makes the second job a no-op instead of a
        // duplicate email: by the time it runs, the first job has already flipped it.
        if (! $followUp || $followUp->status !== 'pending' || $followUp->reminder_sent) {
            return;
        }

        $recipient = $followUp->assignedAdmin ?? $followUp->leadable?->assignedAdmin;

        if (! $recipient || ! $recipient->email) {
            return;
        }

        try {
            Mail::to($recipient->email)->send(new FollowUpReminderMail($followUp));
            $followUp->update(['reminder_sent' => true]);
        } catch (\Throwable $e) {
            Log::error('Follow-up reminder email failed: '.$e->getMessage(), ['follow_up_id' => $followUp->id]);
        }
    }
}
