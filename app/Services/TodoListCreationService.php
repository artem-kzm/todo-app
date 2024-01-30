<?php

namespace App\Services;

use App\Models\TodoList;
use App\Models\User;

class TodoListCreationService
{
    public function createTodoList(string $title, User $asUser): TodoList
    {
        $todoList = new TodoList();
        $todoList->created_by = $asUser->id;
        $todoList->title = $title;
        $todoList->save();

        return $todoList;
    }
}
