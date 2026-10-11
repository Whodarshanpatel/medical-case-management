<?php

namespace App\Policies;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MedicalCasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any medical cases.
     */
    public function viewAny(User $user): bool
    {
        // Both Admin and Viewer can view the case list
        return in_array($user->role, ['Admin', 'Viewer'], true);
    }

    /**
     * Determine whether the user can view the specific medical case.
     */
    public function view(User $user, MedicalCase $case): bool
    {
        // Both Admin and Viewer can view case details
        return in_array($user->role, ['Admin', 'Viewer'], true);
    }

    /**
     * Determine whether the user can create medical cases.
     */
    public function create(User $user): bool
    {
        // Only Admin can create cases
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the medical case.
     */
    public function update(User $user, MedicalCase $case): bool
    {
        // Only Admin can edit cases
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the medical case.
     */
    public function delete(User $user, MedicalCase $case): bool
    {
        // Only Admin can delete cases
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can change the status of the case.
     */
    public function changeStatus(User $user, MedicalCase $case): bool
    {
        // Only Admin can change status
        return $user->isAdmin();
    }
}
