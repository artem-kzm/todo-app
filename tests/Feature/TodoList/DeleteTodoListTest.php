<?php

namespace Tests\Feature\TodoList;

use App\Models\Role;
use App\Models\TodoList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class DeleteTodoListTest extends TestCase
{
    use RefreshDatabase;

    private User $loggedInUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loggedInUser = User::factory()->create();
        Auth::login($this->loggedInUser);
    }

    public function test_assert_delete_todo_list_route_is_protected(): void
    {
        Auth::logout();

        $response = $this->deleteJson('todo_lists/1');
        $response->assertUnauthorized();
    }

    public function test_delete_nonexistent_todo_list(): void
    {
        $response = $this->deleteJson('todo_lists/1');
        $response->assertNotFound();
    }

    public function test_delete_a_todo_list_of_another_user(): void
    {
        /** @var User $anotherUser */
        $anotherUser = User::factory()->create();

        /** @var TodoList $todoList */
        $todoList = TodoList::factory()->create(['created_by' => $anotherUser->id]);

        $response = $this->deleteJson("todo_lists/{$todoList->id}");
        $response->assertForbidden();
    }

    public function test_delete_a_todo_list_of_another_user_as_administrator(): void
    {
        /** @var User $anotherUser */
        $anotherUser = User::factory()->create();

        /** @var TodoList $todoList */
        $todoList = TodoList::factory()->create(['created_by' => $anotherUser->id]);

        $administrator = User::factory()->create(['role' => Role::ADMINISTRATOR_ROLE]);
        Auth::login($administrator);

        $response = $this->deleteJson("todo_lists/{$todoList->id}");
        $response->assertOk();

        $this->assertSoftDeleted('todo_lists', ['id' => $todoList->id]);
    }

    public function test_delete_a_todo_list_room_as_user(): void
    {
        /** @var TodoList $todoList */
        $todoList = TodoList::factory()->create(['created_by' => $this->loggedInUser->id]);

        $response = $this->deleteJson("todo_lists/{$todoList->id}");
        $response->assertOk();

        $this->assertSoftDeleted('todo_lists', ['id' => $todoList->id]);
    }
}
