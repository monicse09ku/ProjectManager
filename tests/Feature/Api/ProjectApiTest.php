<?php

use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create admin user
    $this->admin = User::factory()->create([
        'role' => 'admin',
    ]);

    // Create regular user
    $this->user = User::factory()->create([
        'role' => 'user',
    ]);

    // Create a test client
    $this->client = Client::factory()->create();
});

test('admin can create a project via api', function () {
    $projectData = [
        'client_id' => $this->client->id,
        'title' => 'Test Project API',
        'start_date' => now()->format('Y-m-d'),
        'end_date' => now()->addDays(30)->format('Y-m-d'),
        'status' => 'pending',
    ];

    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->postJson('/api/projects', $projectData);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Project created successfully',
        ]);

    $this->assertDatabaseHas('projects', [
        'title' => 'Test Project API',
        'client_id' => $this->client->id,
    ]);
});

test('user cannot create a project via api', function () {
    $projectData = [
        'client_id' => $this->client->id,
        'title' => 'Test Project',
        'start_date' => now()->format('Y-m-d'),
        'end_date' => now()->addDays(30)->format('Y-m-d'),
        'status' => 'pending',
    ];

    $response = $this
        ->actingAs($this->user, 'sanctum')
        ->postJson('/api/projects', $projectData);

    $response->assertStatus(403);
});

test('guest cannot create a project via api', function () {
    $projectData = [
        'client_id' => $this->client->id,
        'title' => 'Test Project',
        'start_date' => now()->format('Y-m-d'),
        'end_date' => now()->addDays(30)->format('Y-m-d'),
        'status' => 'pending',
    ];

    $response = $this->postJson('/api/projects', $projectData);

    $response->assertStatus(401);
});

test('api project title is required', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->postJson('/api/projects', [
            'client_id' => $this->client->id,
            'title' => '',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'pending',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('title');
});

test('api project client_id is required', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->postJson('/api/projects', [
            'client_id' => '',
            'title' => 'Test Project',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'pending',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('client_id');
});

test('api project client_id must exist in clients table', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->postJson('/api/projects', [
            'client_id' => 99999,
            'title' => 'Test Project',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'pending',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('client_id');
});

test('api project start_date is required', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->postJson('/api/projects', [
            'client_id' => $this->client->id,
            'title' => 'Test Project',
            'start_date' => '',
            'end_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'pending',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('start_date');
});

test('api project start_date cannot be in the past', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->postJson('/api/projects', [
            'client_id' => $this->client->id,
            'title' => 'Test Project',
            'start_date' => now()->subDays(1)->format('Y-m-d'),
            'end_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'pending',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('start_date');
});

test('api project end_date is required', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->postJson('/api/projects', [
            'client_id' => $this->client->id,
            'title' => 'Test Project',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => '',
            'status' => 'pending',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('end_date');
});

test('api project end_date must be after start_date', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->postJson('/api/projects', [
            'client_id' => $this->client->id,
            'title' => 'Test Project',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
            'status' => 'pending',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('end_date');
});

test('api project status is required', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->postJson('/api/projects', [
            'client_id' => $this->client->id,
            'title' => 'Test Project',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => '',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('status');
});

test('admin can view all projects via api', function () {
    Project::factory()->count(3)->create();

    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->getJson('/api/projects');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Projects retrieved successfully',
        ])
        ->assertJsonCount(3, 'data');
});

test('user cannot view projects via api', function () {
    $response = $this
        ->actingAs($this->user, 'sanctum')
        ->getJson('/api/projects');

    $response->assertStatus(403);
});

test('admin can view a single project via api', function () {
    $project = Project::factory()->create();

    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->getJson("/api/projects/{$project->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Project retrieved successfully',
            'data' => [
                'id' => $project->id,
                'title' => $project->title,
            ]
        ]);
});

test('admin can update a project via api', function () {
    $project = Project::factory()->create(['title' => 'Old Title']);

    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->putJson("/api/projects/{$project->id}", [
            'client_id' => $project->client_id,
            'title' => 'New Title',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'in_progress',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Project updated successfully',
        ]);

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'title' => 'New Title',
    ]);
});

test('user cannot update a project via api', function () {
    $project = Project::factory()->create();

    $response = $this
        ->actingAs($this->user, 'sanctum')
        ->putJson("/api/projects/{$project->id}", [
            'client_id' => $project->client_id,
            'title' => 'New Title',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'completed',
        ]);

    $response->assertStatus(403);
});

test('admin can delete a project via api', function () {
    $project = Project::factory()->create();

    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->deleteJson("/api/projects/{$project->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Project deleted successfully',
        ]);

    $this->assertDatabaseMissing('projects', [
        'id' => $project->id,
    ]);
});

test('user cannot delete a project via api', function () {
    $project = Project::factory()->create();

    $response = $this
        ->actingAs($this->user, 'sanctum')
        ->deleteJson("/api/projects/{$project->id}");

    $response->assertStatus(403);
});

test('api returns error when deleting non-existent project', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->deleteJson('/api/projects/99999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Project not found',
        ]);
});

test('api returns error when updating non-existent project', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->putJson('/api/projects/99999', [
            'client_id' => $this->client->id,
            'title' => 'Test',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'pending',
        ]);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Project not found',
        ]);
});

test('admin can view tasks for a specific project via api', function () {
    $project = Project::factory()->create();

    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->getJson("/api/projects/{$project->id}/tasks");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Project tasks retrieved successfully',
        ]);
});
