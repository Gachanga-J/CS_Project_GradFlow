<?php

namespace App\Console\Commands;

use App\Services\DeadlineReminderService;
use Illuminate\Console\Command;

class SendDeadlineReminders extends Command
{
    protected $signature   = 'notifications:send-deadline-reminders';
    protected $description = 'Send deadline reminders at 3 days, 1 day before, and overdue notifications';

    public function handle(DeadlineReminderService $reminderService): void
    {
        $sent = $reminderService->sendAll();

        $this->info("Sent {$sent} reminder notification(s) across all departments.");
    }
}
