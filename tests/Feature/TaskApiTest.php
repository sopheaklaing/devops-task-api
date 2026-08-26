<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create authenticated user
        $this->user = User::factory()->create();

        // Create JWT token for the user
        $this->token = JWTAuth::fromUser($this->user);
    }

    /**
     * Get authentication headers.
     */
    private function authHeaders(): array
    {
        return [
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ];
    }

    /**
     * Test: authenticated user can get tasks.
     */
    public function test_can_get_tasks(): void
    {
        $response = $this
            ->withHeaders($this->authHeaders())
            ->getJson('/api/tasks');

        $response->assertStatus(200);
    }

    /**
     * Test: authenticated user can create a task.
     */
    public function test_can_create_task(): void
    {
        $response = $this
            ->withHeaders($this->authHeaders())
            ->postJson('/api/tasks', [
                'title' => 'Learn Docker',
                'description' => 'Learn Docker Compose and CI/CD',
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('tasks', [
            'user_id' => $this->user->id,
            'title' => 'Learn Docker',
            'description' => 'Learn Docker Compose and CI/CD',
        ]);
    }

    /**
     * Test: authenticated user can get a single task.
     */
    public function test_can_get_single_task(): void
    {
        $task = Task::create([
            'user_id' => $this->user->id,
            'title' => 'Learn Docker',
            'description' => 'Learn Docker Compose and CI/CD',
        ]);
        $response = $this
            ->withHeaders($this->authHeaders())
            ->getJson('/api/tasks/'.$task->id);

        $response->assertStatus(200);

        $response->assertJson([
            'id' => $task->id,
            'user_id' => $this->user->id,
            'title' => 'Learn Docker',
            'description' => 'Learn Docker Compose and CI/CD',
        ]);
    }

    /**
     * Test: authenticated user can update their task.
     */
    public function test_can_update_task(): void
    {
        $task = Task::create([
            'user_id' => $this->user->id,
            'title' => 'Learn Docker',
            'description' => 'Learn Docker Compose',
        ]);
        $response = $this
            ->withHeaders($this->authHeaders())
            ->putJson('/api/tasks/'.$task->id, [
                'title' => 'Learn Docker CI/CD',
                'description' => 'Learn Docker, Compose and GitHub Actions',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'user_id' => $this->user->id,
            'title' => 'Learn Docker CI/CD',
            'description' => 'Learn Docker, Compose and GitHub Actions',
        ]);
    }

    /**
     * Test: authenticated user can delete their task.
     */
    public function test_can_delete_task(): void
    {
        $task = Task::create([
            'user_id' => $this->user->id,
            'title' => 'Learn Docker',
            'description' => 'Learn Docker Compose',
        ]);

        $response = $this
            ->withHeaders($this->authHeaders())
            ->deleteJson('/api/tasks/'.$task->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_get_tasks(): void
    {
        $response = $this->getJson('/api/tasks');

        $response->assertStatus(401);
    }

    public function test_cannot_create_task_without_title(): void
    {
        $response = $this
            ->withHeaders($this->authHeaders())
            ->postJson('/api/tasks', [
                'description' => 'Learn Docker',
            ]);

        $response->assertStatus(422);
    }

    public function test_user_cannot_view_another_users_task(): void
    {
        $anotherUser = User::factory()->create();

        $task = Task::create([
            'user_id' => $anotherUser->id,
            'title' => 'Private Task',
            'description' => 'Private',
        ]);

        $response = $this
            ->withHeaders($this->authHeaders())
            ->getJson('/api/tasks/'.$task->id);

        $response->assertStatus(403);
    }
}
