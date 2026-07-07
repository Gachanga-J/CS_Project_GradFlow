<?php

namespace App\Services;

use App\Models\Milestone;
use App\Models\User;
use App\Notifications\MilestoneDeadlineReminder;

class DeadlineReminderService
{
    /**
     * Send all due reminders (3-day, 1-day, overdue), optionally scoped to one department.
     * Returns the number of notifications actually sent.
     */
    public function sendAll(?int $departmentId = null): int
    {
        $sent = 0;

        $sent += $this->sendReminders(3, 'reminder', $departmentId);
        $sent += $this->sendReminders(1, 'reminder', $departmentId);
        $sent += $this->sendOverdue($departmentId);

        return $sent;
    }

    private function sendReminders(int $daysAhead, string $type, ?int $departmentId): int
    {
        $sent = 0;

        $milestones = Milestone::where('status', 'open')
            ->whereDate('deadline', now()->addDays($daysAhead)->toDateString())
            ->when($departmentId, fn ($query) => $query->where('department_id', $departmentId))
            ->get();

        foreach ($milestones as $milestone) {
            $students = User::where('role', 'student')
                ->where('department_id', $milestone->department_id)
                ->whereHas('student', function ($q) use ($milestone) {
                    $q->where('intake_year', $milestone->intake_year)
                      ->where('year_of_study', $milestone->year_of_study);
                })
                ->get();

            foreach ($students as $student) {
                $alreadyNotified = $student->notifications()
                    ->where('type', MilestoneDeadlineReminder::class)
                    ->whereJsonContains('data->milestone_id', $milestone->id)
                    ->whereJsonContains('data->days_left', $daysAhead)
                    ->exists();

                if ($alreadyNotified) {
                    continue;
                }

                $student->notify(new MilestoneDeadlineReminder($milestone, $type));
                $sent++;
            }
        }

        return $sent;
    }

    private function sendOverdue(?int $departmentId): int
    {
        $sent = 0;

        $milestones = Milestone::where('status', 'open')
            ->whereDate('deadline', now()->subDay()->toDateString())
            ->when($departmentId, fn ($query) => $query->where('department_id', $departmentId))
            ->get();

        foreach ($milestones as $milestone) {
            $students = User::where('role', 'student')
                ->where('department_id', $milestone->department_id)
                ->whereHas('student', function ($q) use ($milestone) {
                    $q->where('intake_year', $milestone->intake_year)
                      ->where('year_of_study', $milestone->year_of_study);
                })
                ->get();

            foreach ($students as $student) {
                $alreadyNotified = $student->notifications()
                    ->where('type', MilestoneDeadlineReminder::class)
                    ->whereJsonContains('data->milestone_id', $milestone->id)
                    ->whereJsonContains('data->type', 'overdue')
                    ->exists();

                if ($alreadyNotified) {
                    continue;
                }

                $student->notify(new MilestoneDeadlineReminder($milestone, 'overdue'));
                $sent++;
            }
        }

        return $sent;
    }
}
