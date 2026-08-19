<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_tasks(): void
    {
        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200);
    }

    public function test_can_create_task(): void
    {
        $response = $this->postJson('/api/tasks', [
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

        $response = $this->getJson('/api/tasks/' . $task->id);

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

    $response = $this->putJson('/api/tasks/' . $task->id, [
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

    $response = $this->deleteJson('/api/tasks/' . $task->id);

    $response->assertStatus(204);

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
}
}