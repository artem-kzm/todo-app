<?php

namespace Tests\Feature\TodoList;

use App\Models\TodoList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CreateTodoListItemTest extends TestCase
{
    use RefreshDatabase;

    private User $loggedInUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loggedInUser = User::factory()->create();
        Auth::login($this->loggedInUser);
    }

    public function test_assert_add_todo_list_item_route_is_protected(): void
    {
        Auth::logout();

        $response = $this->postJson('todo_lists/1/items');
        $response->assertUnauthorized();
    }

    public function test_add_a_todo_list_item_to_nonexistent_list(): void
    {
        $response = $this->postJson('todo_lists/1/items');
        $response->assertNotFound();
    }

    public function test_add_a_todo_list_item_with_no_data(): void
    {
        TodoList::factory()->for($this->loggedInUser, 'createdBy')->create();

        $response = $this->postJson('/todo_lists/1/items');

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['title' => 'The title field is required.']);
    }

    public function test_add_a_todo_list_item_with_wrong_title_format(): void
    {
        $data = ['title' => ['buy an apple']];
        $response = $this->postJson('/todo_lists', $data);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['title' => 'The title must be a string.']);
    }

    public function test_add_a_todo_list_item_with_invalid_title_type(): void
    {
        $todoList = TodoList::factory()->for($this->loggedInUser, 'createdBy')->create();

        $response = $this->postJson("/todo_lists/{$todoList->id}/items", ['title' => 123]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['title' => 'The title must be a string.']);
    }

    public function test_add_a_todo_list_item_with_too_long_title(): void
    {
        $todoList = TodoList::factory()->for($this->loggedInUser, 'createdBy')->create();

        $longString = str_repeat('a', 256);

        $response = $this->postJson("/todo_lists/{$todoList->id}/items", ['title' => $longString]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['title' => 'The title must not be greater than 255 characters.']);
    }

    public function test_add_a_todo_list_item_to_non_owned_list(): void
    {
        $todoList = TodoList::factory()->for($this->loggedInUser, 'createdBy')->create();

        $anotherUser = User::factory()->create();
        Auth::login($anotherUser);

        $response = $this->postJson("/todo_lists/{$todoList->id}/items", ['title' => 'buy milk']);

        $response->assertForbidden();
    }

    public function test_add_a_todo_list_item_to_non_owned_ist(): void
    {
        /** @var TodoList $todoList */
        $todoList = TodoList::factory()->for($this->loggedInUser, 'createdBy')->create();

        $title = 'go to the gym';

        $response = $this->postJson("/todo_lists/{$todoList->id}/items", ['title' => $title]);
        $response->assertOk();

        $this->assertDatabaseHas('todo_list_items', [
            'title' => $title,
            'todo_list_id' => $todoList->id
        ]);
    }
}
