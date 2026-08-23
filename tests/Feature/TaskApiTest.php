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

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();

        $this->token = JWTAuth::fromUser($user);
    }

    private function authHeaders(): array
    {
        return [
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ];
    }

    public function test_can_get_tasks(): void
    {
        $response = $this
            ->withHeaders($this->authHeaders())
            ->getJson('/api/tasks');

        $response->assertStatus(200);
    }

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
            'title' => 'Learn Docker',
            'description' => 'Learn Docker Compose and CI/CD',
        ]);
    }

    public function test_can_get_single_task(): void
    {
        $task = Task::create([
            'title' => 'Learn Docker',
            'description' => 'Learn Docker Compose and CI/CD',
        ]);

        $response = $this
            ->withHeaders($this->authHeaders())
            ->getJson('/api/tasks/'.$task->id);

        $response->assertStatus(200);

        $response->assertJson([
            'id' => $task->id,
            'title' => 'Learn Docker',
            'description' => 'Learn Docker Compose and CI/CD',
        ]);
    }

    public function test_can_update_task(): void
    {
        $task = Task::create([
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
            'title' => 'Learn Docker CI/CD',
            'description' => 'Learn Docker, Compose and GitHub Actions',
        ]);
    }

    public function test_can_delete_task(): void
    {
        $task = Task::create([
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
}