<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        // Admin can view all tasks
        if ($user->role === 'admin') {
            return true;
        }

        // User can only view their assigned tasks
        return $task->assigned_user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        // Admin can update all tasks
        if ($user->role === 'admin') {
            return true;
        }

        // User can only update their assigned tasks
        return $task->assigned_user_id === $user->id;
    }

    /**
     * Determine whether the user can update specific fields in the task.
     * Users can only update deadline and status, admins can update all.
     */
    public function updateFields(User $user, Task $task, array $fields): bool
    {
        // Admin can update any field
        if ($user->role === 'admin') {
            return true;
        }

        // User can only update if it's their assigned task
        if ($task->assigned_user_id !== $user->id) {
            return false;
        }

        // User can only update deadline and status
        $allowedFields = ['deadline', 'status'];
        foreach ($fields as $field) {
            if (!in_array($field, $allowedFields)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return $user->role === 'admin';
    }
}
