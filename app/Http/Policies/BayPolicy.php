<?php
// app/Policies/BayPolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\Bay;

class BayPolicy
{
    public function viewAny(User $user)
    {
        return in_array($user->role, ['admin', 'supervisor', 'worker']);
    }

    public function view(User $user, Bay $bay)
    {
        return in_array($user->role, ['admin', 'supervisor', 'worker']);
    }

    public function create(User $user)
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Bay $bay)
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Bay $bay)
    {
        return $user->role === 'admin' && $bay->jobEntries()->count() === 0;
    }
}