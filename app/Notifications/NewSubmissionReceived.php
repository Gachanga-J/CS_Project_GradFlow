<?php

namespace App\Notifications;

use App\Models\MilestoneSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewSubmissionReceived extends Notification
{
    use Queueable;

    public function __construct(
        public MilestoneSubmission $submission
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $milestone = $this->submission->milestone;
        $student   = $this->submission->student;

        return [
            'submission_id'   => $this->submission->id,
            'milestone_id'    => $milestone->id,
            'milestone_title' => $milestone->title,
            'student_name'    => $student->user->full_name,
            'message'         => "{$student->user->full_name} submitted \"{$milestone->title}\" for your review.",
            'route'           => route('supervisor.submissions.index'),
        ];
    }
}
