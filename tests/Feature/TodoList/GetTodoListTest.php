<?php

namespace Tests\Feature\TodoList;

use App\Models\TodoList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class GetTodoListTest extends TestCase
{
    use RefreshDatabase;

    public function test_assert_get_todo_lists_route_is_protected(): void
    {
        $response = $this->getJson('todo_lists');
        $response->assertUnauthorized();
    }

    public function test_get_todo_lists(): void
    {
        $user = User::factory()->userRole()->create();
        $anotherUser = User::factory()->create();
        Auth::login($user);

        $todoLists = TodoList::factory()->count(3)->for($user, 'createdBy')->create();

        TodoList::factory()->count(3)->deleted()->for($user, 'createdBy')->create(); // deleted todos
        TodoList::factory()->count(3)->for($anotherUser, 'createdBy')->create(); // another user todos

        $response = $this->getJson('/todo_lists');
        $response->assertOk();
        $response->assertExactJson($todoLists->toArray());
    }

    public function test_get_todo_lists_as_administrator(): void
    {
        $administratorUser = User::factory()->administrator()->create();
        $anotherUser = User::factory()->create();
        Auth::login($administratorUser);

        $todoLists = TodoList::factory()->count(3)->for($administratorUser, 'createdBy')->create();

        $deletedTodoLists = TodoList::factory()
            ->count(3)
            ->deleted()
            ->for($administratorUser, 'createdBy')
            ->create();

        $anotherTodoLists = TodoList::factory()
            ->count(3)
            ->for($anotherUser, 'createdBy')
            ->create();

        $anotherDeletedTodoLists = TodoList::factory()
            ->count(3)
            ->deleted()
            ->for($anotherUser, 'createdBy')
            ->create();

        $merged = $todoLists->merge($deletedTodoLists)
            ->merge($anotherTodoLists)
            ->merge($anotherDeletedTodoLists);

        $response = $this->getJson('/todo_lists');
        $response->assertOk();
        $response->assertExactJson($merged->toArray());
    }
}
