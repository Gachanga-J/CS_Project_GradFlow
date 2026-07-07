<?php

namespace App\Console\Commands;

use App\Models\Milestone;
use App\Models\User;
use App\Notifications\MilestoneDeadlineReminder;
use Illuminate\Console\Command;

class SendDeadlineReminders extends Command
{
    protected $signature   = 'notifications:send-deadline-reminders';
    protected $description = 'Send deadline reminders at 3 days, 1 day before, and overdue notifications';

    public function handle(): void
    {
        $this->sendReminders(3, 'reminder');
        $this->sendReminders(1, 'reminder');
        $this->sendOverdue();
    }

    private function sendReminders(int $daysAhead, string $type): void
    {
        $milestones = Milestone::where('status', 'open')
            ->whereDate('deadline', now()->addDays($daysAhead)->toDateString())
            ->get();

        foreach ($milestones as $milestone) {
            $students = User::where('role', 'student')
                ->where('department_id', $milestone->department_id)
                ->whereHas('student', fn($q) => $q
                    ->where('intake_year', $milestone->intake_year)
                    ->where('year_of_study', $milestone->year_of_study)
                )
                ->get();

            foreach ($students as $student) {
                $alreadyNotified = $student->notifications()
                    ->where('type', MilestoneDeadlineReminder::class)
                    ->whereJsonContains('data->milestone_id', $milestone->id)
                    ->whereJsonContains('data->days_left', $daysAhead)
                    ->exists();

                if ($alreadyNotified) continue;

                $student->notify(new MilestoneDeadlineReminder($milestone, $type));
                $this->info("({$daysAhead}d reminder) Notified {$student->full_name} about: {$milestone->title}");
            }
        }
    }

    private function sendOverdue(): void
    {
        $milestones = Milestone::where('status', 'open')
            ->whereDate('deadline', now()->subDay()->toDateString())
            ->get();

        foreach ($milestones as $milestone) {
            $students = User::where('role', 'student')
                ->where('department_id', $milestone->department_id)
                ->whereHas('student', fn($q) => $q
                    ->where('intake_year', $milestone->intake_year)
                    ->where('year_of_study', $milestone->year_of_study)
                )
                ->get();

            foreach ($students as $student) {
                $alreadyNotified = $student->notifications()
                    ->where('type', MilestoneDeadlineReminder::class)
                    ->whereJsonContains('data->milestone_id', $milestone->id)
                    ->whereJsonContains('data->type', 'overdue')
                    ->exists();

                if ($alreadyNotified) continue;

                $student->notify(new MilestoneDeadlineReminder($milestone, 'overdue'));
                $this->info("(overdue) Notified {$student->full_name} about: {$milestone->title}");
            }
        }
    }
}
