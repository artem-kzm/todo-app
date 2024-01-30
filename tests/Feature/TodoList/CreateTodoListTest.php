<?php

namespace Tests\Feature\TodoList;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CreateTodoListTest extends TestCase
{
    use RefreshDatabase;

    private User $loggedInUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loggedInUser = User::factory()->create();
        Auth::login($this->loggedInUser);
    }

    public function test_assert_create_todo_list_route_is_protected(): void
    {
        Auth::logout();

        $response = $this->postJson('todo_lists');
        $response->assertUnauthorized();
    }

    public function test_create_a_todo_list_with_no_data(): void
    {
        $response = $this->postJson('/todo_lists');

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['title' => 'The title field is required.']);
    }

    public function test_create_a_todo_list_with_wrong_title_format(): void
    {
        $data = ['title' => ['for work']];
        $response = $this->postJson('/todo_lists', $data);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['title' => 'The title must be a string.']);
    }

    public function test_create_a_todo_list_with_invalid_title_type(): void
    {
        $response = $this->postJson('/todo_lists', ['title' => 123]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['title' => 'The title must be a string.']);
    }

    public function test_create_a_todo_list_with_too_long_title(): void
    {
        $longString = str_repeat('a', 256);

        $response = $this->postJson('/todo_lists', ['title' => $longString]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['title' => 'The title must not be greater than 255 characters.']);
    }

    public function test_create_a_todo_list(): void
    {
        $title = 'homework';

        $response = $this->postJson('/todo_lists', ['title' => $title]);
        $response->assertOk();

        $this->assertDatabaseHas('todo_lists', [
            'title' => $title,
            'created_by' => $this->loggedInUser->id
        ]);
    }
}
