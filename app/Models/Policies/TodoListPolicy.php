<?php

namespace App\Models\Policies;

use App\Models\TodoList;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TodoListPolicy
{
    use HandlesAuthorization;

    public function delete(User $user, TodoList $todoList): bool
    {
        return $user->isAdministrator() || $user->id === $todoList->created_by;
    }

    public function addItem(User $user, TodoList $todoList): bool
    {
        return $user->id === $todoList->created_by;
    }

    public function getItems(User $user, TodoList $todoList): bool
    {
        return $user->isAdministrator() || $user->id === $todoList->created_by;
    }

    public function deleteItems(User $user, TodoList $todoList): bool
    {
        return $user->isAdministrator() || $user->id === $todoList->created_by;
    }

    public function markItemsAsDone(User $user, TodoList $todoList): bool
    {
        return $user->id === $todoList->created_by;
    }
}
