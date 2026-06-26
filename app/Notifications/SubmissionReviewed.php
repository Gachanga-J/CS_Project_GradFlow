<?php

namespace App\Notifications;

use App\Models\MilestoneSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubmissionReviewed extends Notification
{
    use Queueable;

    public function __construct(
        public MilestoneSubmission $submission,
        public string $decision  // 'approved' | 'rejected'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $milestone = $this->submission->milestone;

        return [
            'submission_id'  => $this->submission->id,
            'milestone_id'   => $milestone->id,
            'milestone_title'=> $milestone->title,
            'decision'       => $this->decision,
            'feedback'       => $this->submission->supervisor_feedback,
            'message'        => $this->decision === 'approved'
                ? "Your submission for \"{$milestone->title}\" has been approved by your supervisor."
                : "Your submission for \"{$milestone->title}\" was rejected. Please review the feedback and resubmit.",
        ];
    }
}
