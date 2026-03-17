<?php
// app/Policies/JobEntryPolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\JobEntry;

class JobEntryPolicy
{
    public function viewAny(User $user)
    {
        return in_array($user->role, ['admin', 'supervisor', 'worker']);
    }

    public function view(User $user, JobEntry $jobEntry)
    {
        return $user->role === 'admin' || $user->role === 'supervisor' || $user->id === $jobEntry->user_id;
    }

    public function create(User $user)
    {
        return in_array($user->role, ['admin', 'supervisor', 'worker']);
    }

    public function update(User $user, JobEntry $jobEntry)
    {
        return $user->role === 'admin' || ($user->role === 'supervisor' && $jobEntry->status === 'submitted');
    }

    public function delete(User $user, JobEntry $jobEntry)
    {
        return $user->role === 'admin' || ($user->id === $jobEntry->user_id && $jobEntry->status === 'submitted');
    }

    public function approve(User $user, JobEntry $jobEntry)
    {
        return in_array($user->role, ['admin', 'supervisor']);
    }
}