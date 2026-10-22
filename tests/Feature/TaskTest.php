<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_tasks_index(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/tasks');

        $response->assertStatus(200);
    }

    public function test_guest_cannot_view_tasks(): void
    {
        $response = $this->get('/tasks');

        $response->assertRedirect('/login');
    }

    public function test_user_can_create_task(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/tasks', [
            'title' => 'Test Task',
            'description' => 'Test description',
            'priority' => 'high',
            'due_date' => now()->addDays(7)->format('Y-m-d'),
        ]);

        $response->assertRedirect('/tasks');
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'user_id' => $user->id,
            'priority' => 'high',
        ]);
    }

    public function test_task_title_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/tasks', [
            'title' => '',
            'priority' => 'medium',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_user_can_update_their_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->patch("/tasks/{$task->id}", [
            'title' => 'Updated Title',
            'priority' => 'low',
        ]);

        $response->assertRedirect('/tasks');
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'priority' => 'low',
        ]);
    }

    public function test_user_cannot_update_other_users_task(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2)->patch("/tasks/{$task->id}", [
            'title' => 'Hacked Title',
        ]);

        $response->assertForbidden();
    }

    public function test_user_can_delete_their_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/tasks/{$task->id}");

        $response->assertRedirect('/tasks');
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_can_mark_task_as_completed(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->pending()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post("/tasks/{$task->id}/complete");

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'completed',
        ]);
        $task->refresh();
        $this->assertNotNull($task->completed_at);
    }

    public function test_user_can_mark_task_as_pending(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->completed()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post("/tasks/{$task->id}/uncomplete");

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'pending',
        ]);
        $task->refresh();
        $this->assertNull($task->completed_at);
    }

    public function test_tasks_are_filtered_by_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        Task::factory()->create(['user_id' => $user1->id, 'title' => 'User 1 Task']);
        Task::factory()->create(['user_id' => $user2->id, 'title' => 'User 2 Task']);

        $response = $this->actingAs($user1)->get('/tasks');

        $response->assertSee('User 1 Task');
        $response->assertDontSee('User 2 Task');
    }

    public function test_overdue_scope_works(): void
    {
        $user = User::factory()->create();
        
        $overdueTask = Task::factory()->pending()->create([
            'user_id' => $user->id,
            'due_date' => now()->subDays(1),
        ]);

        $futureTask = Task::factory()->pending()->create([
            'user_id' => $user->id,
            'due_date' => now()->addDays(1),
        ]);

        $overdueTasks = Task::overdue()->get();

        $this->assertTrue($overdueTasks->contains($overdueTask));
        $this->assertFalse($overdueTasks->contains($futureTask));
    }
}
