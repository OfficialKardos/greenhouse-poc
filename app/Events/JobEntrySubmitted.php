<?php
// app/Events/JobEntrySubmitted.php

namespace App\Events;

use App\Models\JobEntry;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobEntrySubmitted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $jobEntry;

    public function __construct(JobEntry $jobEntry)
    {
        $this->jobEntry = $jobEntry;
    }

    public function broadcastOn()
    {
        return new Channel('jobs');
    }

    public function broadcastAs()
    {
        return 'job.submitted';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->jobEntry->id,
            'job_number' => $this->jobEntry->job_number,
            'user' => $this->jobEntry->user->name,
            'job_type' => $this->jobEntry->jobType->name,
            'time' => $this->jobEntry->submitted_at->toDateTimeString()
        ];
    }
}