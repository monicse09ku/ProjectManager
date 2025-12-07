<?php

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create admin user
    $this->admin = User::factory()->create([
        'role' => 'admin',
    ]);

    // Create regular users
    $this->user = User::factory()->create([
        'role' => 'user',
    ]);
    
    $this->otherUser = User::factory()->create([
        'role' => 'user',
    ]);

    // Create a test project
    $this->project = Project::factory()->create();
});

test('admin can create a task via api', function () {
    $taskData = [
        'project_id' => $this->project->id,
        'assigned_user_id' => $this->user->id,
        'title' => 'Test Task API',
        'deadline' => now()->addDays(7)->format('Y-m-d'),
        'status' => 'pending',
    ];

    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->postJson('/api/tasks', $taskData);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Task created successfully',
        ]);

    $this->assertDatabaseHas('tasks', [
        'title' => 'Test Task API',
        'project_id' => $this->project->id,
        'assigned_user_id' => $this->user->id,
    ]);
});

test('user cannot create a task via api', function () {
    $taskData = [
        'project_id' => $this->project->id,
        'assigned_user_id' => $this->user->id,
        'title' => 'Test Task',
        'deadline' => now()->addDays(7)->format('Y-m-d'),
        'status' => 'pending',
    ];

    $response = $this
        ->actingAs($this->user, 'sanctum')
        ->postJson('/api/tasks', $taskData);

    $response->assertStatus(403);
});

test('guest cannot create a task via api', function () {
    $taskData = [
        'project_id' => $this->project->id,
        'assigned_user_id' => $this->user->id,
        'title' => 'Test Task',
        'deadline' => now()->addDays(7)->format('Y-m-d'),
        'status' => 'pending',
    ];

    $response = $this->postJson('/api/tasks', $taskData);

    $response->assertStatus(401);
});

test('api task title is required', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->postJson('/api/tasks', [
            'project_id' => $this->project->id,
            'assigned_user_id' => $this->user->id,
            'title' => '',
            'deadline' => now()->addDays(7)->format('Y-m-d'),
            'status' => 'pending',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('title');
});

test('api task project_id is required', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->postJson('/api/tasks', [
            'project_id' => '',
            'assigned_user_id' => $this->user->id,
            'title' => 'Test Task',
            'deadline' => now()->addDays(7)->format('Y-m-d'),
            'status' => 'pending',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('project_id');
});

test('api task project_id must exist in projects table', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->postJson('/api/tasks', [
            'project_id' => 99999,
            'assigned_user_id' => $this->user->id,
            'title' => 'Test Task',
            'deadline' => now()->addDays(7)->format('Y-m-d'),
            'status' => 'pending',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('project_id');
});

test('api task assigned_user_id is required', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->postJson('/api/tasks', [
            'project_id' => $this->project->id,
            'assigned_user_id' => '',
            'title' => 'Test Task',
            'deadline' => now()->addDays(7)->format('Y-m-d'),
            'status' => 'pending',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('assigned_user_id');
});

test('api task assigned_user_id must exist in users table', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->postJson('/api/tasks', [
            'project_id' => $this->project->id,
            'assigned_user_id' => 99999,
            'title' => 'Test Task',
            'deadline' => now()->addDays(7)->format('Y-m-d'),
            'status' => 'pending',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('assigned_user_id');
});

test('api task deadline is required', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->postJson('/api/tasks', [
            'project_id' => $this->project->id,
            'assigned_user_id' => $this->user->id,
            'title' => 'Test Task',
            'deadline' => '',
            'status' => 'pending',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('deadline');
});

test('api task deadline cannot be in the past', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->postJson('/api/tasks', [
            'project_id' => $this->project->id,
            'assigned_user_id' => $this->user->id,
            'title' => 'Test Task',
            'deadline' => now()->subDays(1)->format('Y-m-d'),
            'status' => 'pending',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('deadline');
});

test('api task status is required', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->postJson('/api/tasks', [
            'project_id' => $this->project->id,
            'assigned_user_id' => $this->user->id,
            'title' => 'Test Task',
            'deadline' => now()->addDays(7)->format('Y-m-d'),
            'status' => '',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('status');
});

test('admin can view all tasks via api', function () {
    Task::factory()->count(5)->create();

    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->getJson('/api/tasks');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Tasks retrieved successfully',
        ])
        ->assertJsonCount(5, 'data');
});

test('user can only view assigned tasks via api', function () {
    Task::factory()->create(['assigned_user_id' => $this->user->id]);
    Task::factory()->create(['assigned_user_id' => $this->user->id]);
    Task::factory()->count(3)->create(['assigned_user_id' => $this->otherUser->id]);

    $response = $this
        ->actingAs($this->user, 'sanctum')
        ->getJson('/api/tasks');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Tasks retrieved successfully',
        ])
        ->assertJsonCount(2, 'data');
});

test('admin can view a single task via api', function () {
    $task = Task::factory()->create();

    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->getJson("/api/tasks/{$task->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Task retrieved successfully',
            'data' => [
                'id' => $task->id,
                'title' => $task->title,
            ]
        ]);
});

test('user can view their assigned task via api', function () {
    $task = Task::factory()->create(['assigned_user_id' => $this->user->id]);

    $response = $this
        ->actingAs($this->user, 'sanctum')
        ->getJson("/api/tasks/{$task->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);
});

test('user cannot view task assigned to another user via api', function () {
    $task = Task::factory()->create(['assigned_user_id' => $this->otherUser->id]);

    $response = $this
        ->actingAs($this->user, 'sanctum')
        ->getJson("/api/tasks/{$task->id}");

    $response->assertStatus(403);
});

test('admin can update all task fields via api', function () {
    $task = Task::factory()->create(['title' => 'Old Title']);

    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->putJson("/api/tasks/{$task->id}", [
            'project_id' => $task->project_id,
            'assigned_user_id' => $task->assigned_user_id,
            'title' => 'New Title',
            'deadline' => now()->addDays(14)->format('Y-m-d'),
            'status' => 'in_progress',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Task updated successfully',
        ]);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'New Title',
    ]);
});

test('user can update only deadline and status of assigned task via api', function () {
    $task = Task::factory()->create([
        'assigned_user_id' => $this->user->id,
        'status' => 'pending',
    ]);

    $response = $this
        ->actingAs($this->user, 'sanctum')
        ->putJson("/api/tasks/{$task->id}", [
            'deadline' => now()->addDays(10)->format('Y-m-d'),
            'status' => 'in_progress',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Task updated successfully',
        ]);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'status' => 'in_progress',
    ]);
});

test('user cannot update task assigned to another user via api', function () {
    $task = Task::factory()->create(['assigned_user_id' => $this->otherUser->id]);

    $response = $this
        ->actingAs($this->user, 'sanctum')
        ->putJson("/api/tasks/{$task->id}", [
            'deadline' => now()->addDays(10)->format('Y-m-d'),
            'status' => 'completed',
        ]);

    $response->assertStatus(403);
});

test('admin can delete a task via api', function () {
    $task = Task::factory()->create();

    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->deleteJson("/api/tasks/{$task->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Task deleted successfully',
        ]);

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});

test('user cannot delete a task via api', function () {
    $task = Task::factory()->create(['assigned_user_id' => $this->user->id]);

    $response = $this
        ->actingAs($this->user, 'sanctum')
        ->deleteJson("/api/tasks/{$task->id}");

    $response->assertStatus(403);
});

test('api returns error when deleting non-existent task', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->deleteJson('/api/tasks/99999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Task not found',
        ]);
});

test('api returns error when updating non-existent task', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->putJson('/api/tasks/99999', [
            'project_id' => $this->project->id,
            'assigned_user_id' => $this->user->id,
            'title' => 'Test',
            'deadline' => now()->addDays(7)->format('Y-m-d'),
            'status' => 'pending',
        ]);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Task not found',
        ]);
});
