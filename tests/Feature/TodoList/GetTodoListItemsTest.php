<?php

namespace Tests\Feature\TodoList;

use App\Models\TodoList;
use App\Models\TodoListItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class GetTodoListItemsTest extends TestCase
{
    use RefreshDatabase;

    public function test_assert_get_todo_list_item_route_is_protected(): void
    {
        $response = $this->getJson('todo_lists/1/items');
        $response->assertUnauthorized();
    }

    public function test_get_todo_list_items_of_nonexistent_list(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $response = $this->getJson('todo_lists/1/items');
        $response->assertNotFound();
    }

    public function test_get_todo_list_items_for_list_of_another_user(): void
    {
        $user = User::factory()->userRole()->create();
        Auth::login($user);
        $anotherUser = User::factory()->create();

        /** @var TodoList $todoList */
        $todoList = TodoList::factory()->for($anotherUser, 'createdBy')->create();

        $response = $this->getJson("todo_lists/{$todoList->id}/items");
        $response->assertForbidden();
    }

    public function test_get_todo_list_items(): void
    {
        $user = User::factory()->userRole()->create();
        Auth::login($user);

        $todoListItemsFactory = TodoListItem::factory()
            ->count(6)
            ->state(new Sequence(['deleted_at' => null], ['deleted_at' => now()]));

        /** @var TodoList $todoList */
        $todoList = TodoList::factory()
            ->for($user, 'createdBy')
            ->has($todoListItemsFactory, 'items')
            ->create();

        // another list, we should not see its items in the response
        TodoList::factory()
            ->for($user, 'createdBy')
            ->has($todoListItemsFactory, 'items')
            ->create();

        $response = $this->getJson("/todo_lists/{$todoList->id}/items");
        $response->assertOk();
        $response->assertExactJson($todoList->items->toArray());
    }

    public function test_get_todo_list_items_as_administrator(): void
    {
        $user = User::factory()->userRole()->create();
        $adminUser = User::factory()->administrator()->create();
        Auth::login($adminUser);

        $todoListItemsFactory = TodoListItem::factory()
            ->count(6)
            ->state(new Sequence(['deleted_at' => null], ['deleted_at' => now()]));

        /** @var TodoList $todoList */
        $todoList = TodoList::factory()
            ->for($user, 'createdBy')
            ->has($todoListItemsFactory, 'items')
            ->create();

        // another list, we should not see its items in the response
        TodoList::factory()
            ->for($user, 'createdBy')
            ->has($todoListItemsFactory, 'items')
            ->create();

        $response = $this->getJson("/todo_lists/{$todoList->id}/items");
        $response->assertOk();

        $todoListItems = TodoListItem::withTrashed()->whereList($todoList)->get();
        $response->assertExactJson($todoListItems->toArray());
    }
}
