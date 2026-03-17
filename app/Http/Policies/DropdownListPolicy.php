<?php
// app/Policies/DropdownListPolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\DropdownList;

class DropdownListPolicy
{
    public function viewAny(User $user)
    {
        return in_array($user->role, ['admin', 'supervisor']);
    }

    public function view(User $user, DropdownList $dropdownList)
    {
        return in_array($user->role, ['admin', 'supervisor']);
    }

    public function create(User $user)
    {
        return $user->role === 'admin';
    }

    public function update(User $user, DropdownList $dropdownList)
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, DropdownList $dropdownList)
    {
        return $user->role === 'admin';
    }
}