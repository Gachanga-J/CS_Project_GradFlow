<?php

namespace App\Notifications;

use App\Models\Milestone;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MilestoneDeadlineReminder extends Notification
{
    use Queueable;

    public function __construct(
        public Milestone $milestone,
        public string $type = 'reminder' // 'reminder' | 'overdue'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $daysLeft = (int) now()->startOfDay()->diffInDays($this->milestone->deadline, false);

        return [
            'milestone_id'    => $this->milestone->id,
            'milestone_title' => $this->milestone->title,
            'deadline'        => $this->milestone->deadline->format('d M Y'),
            'days_left'       => $daysLeft,
            'type'            => $this->type,
            'route'           => route('student.milestones.show', $this->milestone->id),
        ];
    }
}
