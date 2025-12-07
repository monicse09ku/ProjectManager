<?php

use App\Models\User;
use App\Models\Client;
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
});

test('admin can create a client via api', function () {
    $clientData = [
        'client_name' => 'Test Client API',
    ];

    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->postJson('/api/clients', $clientData);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Client created successfully',
        ]);

    $this->assertDatabaseHas('clients', [
        'client_name' => 'Test Client API',
    ]);
});

test('user cannot create a client via api', function () {
    $clientData = [
        'client_name' => 'Test Client',
    ];

    $response = $this
        ->actingAs($this->user, 'sanctum')
        ->postJson('/api/clients', $clientData);

    $response->assertStatus(403);
});

test('guest cannot create a client via api', function () {
    $clientData = [
        'client_name' => 'Test Client',
    ];

    $response = $this->postJson('/api/clients', $clientData);

    $response->assertStatus(401);
});

test('api client name is required', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->postJson('/api/clients', [
            'client_name' => '',
        ]);

    $response->assertStatus(422)
        ->assertJson([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => [
                'client_name' => ['The client name field is required.']
            ]
        ]);
});

test('api client name must be unique', function () {
    Client::factory()->create(['client_name' => 'Existing Client']);

    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->postJson('/api/clients', [
            'client_name' => 'Existing Client',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('client_name');
});

test('api client name must not exceed 255 characters', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->postJson('/api/clients', [
            'client_name' => str_repeat('a', 256),
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('client_name');
});

test('admin can view all clients via api', function () {
    Client::factory()->count(3)->create();

    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->getJson('/api/clients');

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Clients retrieved successfully',
        ])
        ->assertJsonCount(3, 'data');
});

test('user cannot view clients via api', function () {
    $response = $this
        ->actingAs($this->user, 'sanctum')
        ->getJson('/api/clients');

    $response->assertStatus(403);
});

test('admin can update a client via api', function () {
    $client = Client::factory()->create(['client_name' => 'Old Name']);

    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->putJson("/api/clients/{$client->id}", [
            'client_name' => 'New Name',
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Client updated successfully',
        ]);

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'client_name' => 'New Name',
    ]);
});

test('admin can delete a client via api', function () {
    $client = Client::factory()->create();

    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->deleteJson("/api/clients/{$client->id}");

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Client deleted successfully',
        ]);

    $this->assertDatabaseMissing('clients', [
        'id' => $client->id,
    ]);
});

test('api returns error when deleting non-existent client', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->deleteJson('/api/clients/99999');

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Client not found',
        ]);
});

test('api returns error when updating non-existent client', function () {
    $response = $this
        ->actingAs($this->admin, 'sanctum')
        ->putJson('/api/clients/99999', [
            'client_name' => 'Test',
        ]);

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Client not found',
        ]);
});
