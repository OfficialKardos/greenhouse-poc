<?php
// app/Policies/JobFieldPolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\JobField;

class JobFieldPolicy
{
    public function viewAny(User $user)
    {
        return in_array($user->role, ['admin', 'supervisor']);
    }

    public function view(User $user, JobField $jobField)
    {
        return in_array($user->role, ['admin', 'supervisor']);
    }

    public function create(User $user)
    {
        return $user->role === 'admin';
    }

    public function update(User $user, JobField $jobField)
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, JobField $jobField)
    {
        return $user->role === 'admin';
    }
}