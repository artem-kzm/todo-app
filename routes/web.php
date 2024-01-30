<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\TodoListController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'authenticate']);

Route::middleware('auth:web')->group(function () {
    Route::get('/todo_lists', [TodoListController::class, 'getTodoLists']);
    Route::post('/todo_lists', [TodoListController::class, 'createTodoList']);
    Route::delete('/todo_lists/{todoList}', [TodoListController::class, 'deleteTodoList']);

    Route::get('/todo_lists/{todoList}/items', [TodoListController::class, 'getItems']);
    Route::post('/todo_lists/{todoList}/items', [TodoListController::class, 'addItem']);

    Route::delete(
        '/todo_lists/{todoList}/items/{todoListItem}',
        [TodoListController::class, 'deleteTodoListItem']
    );

    Route::post(
        '/todo_lists/{todoList}/items/{todoListItem}/mark_as_done',
        [TodoListController::class, 'markTodoListItemAsDone']
    );
});
