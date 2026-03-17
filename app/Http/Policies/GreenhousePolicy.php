<?php
// app/Policies/GreenhousePolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\Greenhouse;

class GreenhousePolicy
{
    public function viewAny(User $user)
    {
        return in_array($user->role, ['admin', 'supervisor', 'worker']);
    }

    public function view(User $user, Greenhouse $greenhouse)
    {
        return in_array($user->role, ['admin', 'supervisor', 'worker']);
    }

    public function create(User $user)
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Greenhouse $greenhouse)
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Greenhouse $greenhouse)
    {
        return $user->role === 'admin' && $greenhouse->jobEntries()->count() === 0;
    }

    public function restore(User $user, Greenhouse $greenhouse)
    {
        return $user->role === 'admin';
    }

    public function forceDelete(User $user, Greenhouse $greenhouse)
    {
        return $user->role === 'admin';
    }
}