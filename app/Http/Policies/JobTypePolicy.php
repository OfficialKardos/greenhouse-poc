<?php
// app/Policies/JobTypePolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\JobType;

class JobTypePolicy
{
    public function viewAny(User $user)
    {
        return in_array($user->role, ['admin', 'supervisor', 'worker']);
    }

    public function view(User $user, JobType $jobType)
    {
        return in_array($user->role, ['admin', 'supervisor', 'worker']);
    }

    public function create(User $user)
    {
        return $user->role === 'admin';
    }

    public function update(User $user, JobType $jobType)
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, JobType $jobType)
    {
        return $user->role === 'admin' && $jobType->jobEntries()->count() === 0;
    }
}