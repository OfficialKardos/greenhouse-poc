<?php
// app/Listeners/SendJobSubmittedNotification.php

namespace App\Listeners;

use App\Events\JobEntrySubmitted;
use App\Notifications\JobSubmittedNotification;
use App\Models\User;

class SendJobSubmittedNotification
{
    public function handle(JobEntrySubmitted $event)
    {
        // Notify all supervisors
        $supervisors = User::where('role', 'supervisor')->get();
        
        foreach ($supervisors as $supervisor) {
            $supervisor->notify(new JobSubmittedNotification($event->jobEntry));
        }
    }
}