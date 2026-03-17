<?php
// app/Notifications/JobSubmittedNotification.php

namespace App\Notifications;

use App\Models\JobEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobSubmittedNotification extends Notification
{
    use Queueable;

    protected $jobEntry;

    public function __construct(JobEntry $jobEntry)
    {
        $this->jobEntry = $jobEntry;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Job Entry Submitted')
            ->line("Job {$this->jobEntry->job_number} has been submitted.")
            ->line("Type: {$this->jobEntry->jobType->name}")
            ->line("Location: {$this->jobEntry->greenhouse->name} - {$this->jobEntry->bay->name}")
            ->action('View Job', url("/entries/{$this->jobEntry->id}"))
            ->line('Please review and approve.');
    }

    public function toArray($notifiable)
    {
        return [
            'job_entry_id' => $this->jobEntry->id,
            'job_number' => $this->jobEntry->job_number,
            'message' => "New job entry submitted",
            'type' => 'job_submitted'
        ];
    }
}