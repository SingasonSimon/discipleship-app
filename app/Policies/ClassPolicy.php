<?php

namespace App\Policies;

use App\Models\DiscipleshipClass;
use App\Models\User;

class ClassPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view classes (members can browse, mentors can manage)
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DiscipleshipClass $class): bool
    {
        // All authenticated users can view individual classes
        // Members can view to enroll, mentors can view to manage
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admins and pastors can create classes, not mentors
        return $user->isAdmin() || $user->isPastor();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DiscipleshipClass $class): bool
    {
        // Only admins and pastors can update classes, mentors have read-only access
        return $user->isAdmin() || $user->isPastor();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DiscipleshipClass $class): bool
    {
        // Only admins can delete classes, and only if no sessions exist
        return $user->isAdmin() && $class->sessions()->count() === 0;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DiscipleshipClass $class): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DiscipleshipClass $class): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can manage sessions for the class.
     */
    public function manageSessions(User $user, DiscipleshipClass $class): bool
    {
        // Only admins and pastors can manage sessions, mentors have read-only access
        return $user->isAdmin() || $user->isPastor();
    }

    /**
     * Determine whether the user can view attendance for the class.
     */
    public function viewAttendance(User $user, DiscipleshipClass $class): bool
    {
        // Admins and pastors can manage all classes
        if ($user->isAdmin() || $user->isPastor() || $user->isMentor()) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can access class content (sessions, materials).
     * Enrolled members can access their class content.
     */
    public function accessContent(User $user, DiscipleshipClass $class): bool
    {
        // Admins and pastors can always access
        if ($user->isAdmin() || $user->isPastor() || $user->isMentor()) {
            return true;
        }
        
        // Members can access if they are enrolled and approved
        if ($user->isMember()) {
            $member = \App\Models\Member::where('user_id', $user->id)->first();
            if ($member) {
                return $member->enrollments()
                    ->where('class_id', $class->id)
                    ->where('status', 'approved')
                    ->exists();
            }
        }
        
        return false;
    }
}
