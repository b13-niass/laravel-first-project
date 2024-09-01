<?php

namespace App\Policies;

use App\Enums\RoleEnum;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function isBoutiquier(User $user): bool
    {
        return $user->role->role === RoleEnum::BOUTIQUIER->value;
    }

    /**
     * Determine whether the user can create models.
     */
    public function isAdmin(User $user): bool
    {
        return $user->role->role === RoleEnum::ADMIN->value;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Role $role): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Role $role): bool
    {
        //
    }


}
