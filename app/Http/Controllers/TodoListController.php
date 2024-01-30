<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddTodoListItemRequest;
use App\Http\Requests\CreateTodoRequest;
use App\Models\TodoList;
use App\Models\TodoListItem;
use App\Models\User;
use App\Services\TodoListCreationService;
use Illuminate\Http;
use Illuminate\Support\Facades\Gate;

class TodoListController
{
    public function __construct(
        private TodoListCreationService $creationService
    ) {}

    public function getTodoLists(Http\Request $request): Http\Response
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->isAdministrator()) {
            $todoLists = TodoList::withTrashed()->get();
        } else {
            $todoLists = TodoList::belongsToUser($user)->get();
        }

        return response($todoLists);
    }

    public function createTodoList(CreateTodoRequest $request): Http\Response
    {
        $title = $request->getTitle();
        $user = $request->user();

        $todoList = $this->creationService->createTodoList($title, $user);

        return response($todoList);
    }

    public function deleteTodoList(TodoList $todoList): Http\Response
    {
        Gate::authorize('delete', $todoList);

        $todoList->delete();

        return response('');
    }

    public function addItem(AddTodoListItemRequest $request, TodoList $todoList): Http\Response
    {
        Gate::authorize('addItem', $todoList);

        $todoListItem = TodoListItem::createForList($todoList, $request->getTitle());

        return response($todoListItem);
    }

    public function getItems(Http\Request $request, TodoList $todoList): Http\Response
    {
        Gate::authorize('getItems', $todoList);

        /** @var User $user */
        $user = $request->user();

        if ($user->isAdministrator()) {
            $todoListItems = TodoListItem::whereList($todoList)->withTrashed()->get();
        } else {
            $todoListItems = TodoListItem::whereList($todoList)->get();
        }

        return response($todoListItems);
    }

    public function deleteTodoListItem(TodoList $todoList, TodoListItem $todoListItem): Http\Response
    {
        Gate::authorize('deleteItems', $todoList);

        $todoListItem->delete();

        return response('');
    }

    public function markTodoListItemAsDone(TodoList $todoList, TodoListItem $todoListItem): Http\Response
    {
        Gate::authorize('markItemsAsDone', $todoList);

        $todoListItem->markAsDone();

        return response('');
    }
}
