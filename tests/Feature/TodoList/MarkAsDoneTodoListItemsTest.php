<?php

namespace Tests\Feature\TodoList;

use App\Models\TodoList;
use App\Models\TodoListItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class MarkAsDoneTodoListItemsTest extends TestCase
{
    use RefreshDatabase;

    public function test_assert_mark_as_done_route_is_protected(): void
    {
        $response = $this->postJson('todo_lists/1/items/1/mark_as_done');
        $response->assertUnauthorized();
    }

    public function test_mark_as_done_todo_list_item_of_nonexistent_list(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $response = $this->postJson('todo_lists/1/items/1/mark_as_done');
        $response->assertNotFound();
    }

    public function test_mark_as_done_nonexistent_todo_list_item(): void
    {
        $user = User::factory()->create();
        Auth::login($user);
        $todoList = TodoList::factory()->for($user, 'createdBy')->create();

        $response = $this->postJson("todo_lists/{$todoList->id}/items/1/mark_as_done");
        $response->assertNotFound();
    }

    public function test_mark_as_done_for_list_of_another_user(): void
    {
        $user = User::factory()->userRole()->create();
        Auth::login($user);
        $anotherUser = User::factory()->create();

        $todoList = TodoList::factory()
            ->for($anotherUser, 'createdBy')
            ->has(TodoListItem::factory(), 'items')
            ->create();

        $todoListItem = $todoList->items()->first();

        $response = $this->postJson("todo_lists/{$todoList->id}/items/{$todoListItem->id}/mark_as_done");
        $response->assertForbidden();
    }

    public function test_mark_as_done_todo_list_item(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        /** @var TodoList $todoList */
        $todoList = TodoList::factory()
            ->for($user, 'createdBy')
            ->has(TodoListItem::factory(), 'items')
            ->create();

        /** @var TodoListItem $todoListItem */
        $todoListItem = $todoList->items()->first();

        $response = $this->postJson("todo_lists/{$todoList->id}/items/{$todoListItem->id}/mark_as_done");
        $response->assertOk();

        $todoListItem->refresh();
        static::assertTrue($todoListItem->is_done);
    }
}
