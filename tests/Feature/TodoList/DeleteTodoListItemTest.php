<?php

namespace Tests\Feature\TodoList;

use App\Models\Role;
use App\Models\TodoList;
use App\Models\TodoListItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class DeleteTodoListItemTest extends TestCase
{
    use RefreshDatabase;

    private User $loggedInUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loggedInUser = User::factory()->userRole()->create();
        Auth::login($this->loggedInUser);
    }

    public function test_assert_delete_todo_list_item_route_is_protected(): void
    {
        Auth::logout();

        $response = $this->deleteJson('todo_lists/1/items/1');
        $response->assertUnauthorized();
    }

    public function test_delete_nonexistent_todo_list_item(): void
    {
        $todoList = TodoList::factory()->for($this->loggedInUser, 'createdBy')->create();

        $response = $this->deleteJson("todo_lists/{$todoList->id}/items/1");
        $response->assertNotFound();
    }

    public function test_delete_a_todo_list_item_of_nonexistent_list(): void
    {
        /** @var TodoList $todoList */
        TodoList::factory()
            ->for($this->loggedInUser, 'createdBy')
            ->has(TodoListItem::factory(), 'items')
            ->create();

        $response = $this->deleteJson('todo_lists/999/items/1');
        $response->assertNotFound();
    }

    public function test_delete_a_todo_list_item_for_list_of_another_user(): void
    {
        /** @var User $anotherUser */
        $anotherUser = User::factory()->create();

        /** @var TodoList $todoList */
        $todoList = TodoList::factory()
            ->for($anotherUser, 'createdBy')
            ->has(TodoListItem::factory(), 'items')
            ->create();

        /** @var TodoListItem $todoListItem */
        $todoListItem = $todoList->items()->first();

        $response = $this->deleteJson("todo_lists/{$todoList->id}/items/{$todoListItem->id}");
        $response->assertForbidden();
    }

    public function test_delete_a_todo_list_of_another_user_as_administrator(): void
    {
        /** @var User $anotherUser */
        $anotherUser = User::factory()->create();

        /** @var TodoList $todoList */
        $todoList = TodoList::factory()
            ->for($anotherUser, 'createdBy')
            ->has(TodoListItem::factory(), 'items')
            ->create();

        /** @var TodoListItem $todoListItem */
        $todoListItem = $todoList->items()->first();

        $adminUser = User::factory()->administrator()->create();
        Auth::login($adminUser);

        $response = $this->deleteJson("todo_lists/{$todoList->id}/items/{$todoListItem->id}");
        $response->assertOk();

        $this->assertSoftDeleted('todo_list_items', ['id' => $todoListItem->id]);
    }

    public function test_delete_a_todo_list_item_as_user(): void
    {
        /** @var TodoList $todoList */
        $todoList = TodoList::factory()
            ->for($this->loggedInUser, 'createdBy')
            ->has(TodoListItem::factory(), 'items')
            ->create();

        /** @var TodoListItem $todoListItem */
        $todoListItem = $todoList->items()->first();

        $response = $this->deleteJson("todo_lists/{$todoList->id}/items/{$todoListItem->id}");
        $response->assertOk();

        $this->assertSoftDeleted('todo_list_items', ['id' => $todoListItem->id]);
    }
}
