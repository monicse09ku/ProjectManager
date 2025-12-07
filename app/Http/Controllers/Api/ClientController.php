<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Http\Traits\ApiResponse;
use App\Models\Client;
use App\Http\Requests\ClientRequest;

class ClientController extends Controller
{
    use ApiResponse;

    /**
     * Get all clients - Only admins
     */
    public function index()
    {
        $this->authorize('viewAny', Client::class);
        
        $clients = Client::all();
        return $this->success(ClientResource::collection($clients), 'Clients retrieved successfully');
    }

    /**
     * Get a specific client - Only admins
     */
    public function show(Client $client)
    {
        $this->authorize('view', $client);
        
        return $this->success(new ClientResource($client), 'Client retrieved successfully');
    }

    /**
     * Create a new client - Only admins
     */
    public function store(ClientRequest $request)
    {
        $this->authorize('create', Client::class);
        
        $client = Client::create($request->validated());
        return $this->success(new ClientResource($client), 'Client created successfully', 201);
    }

    /**
     * Update a client - Only admins
     */
    public function update(ClientRequest $request, $id)
    {
        $client = Client::find($id);
        
        if (!$client) {
            return $this->error('Client not found', null, 404);
        }
        
        $this->authorize('update', $client);
        $client->update($request->validated());
        return $this->success(new ClientResource($client), 'Client updated successfully');
    }

    /**
     * Delete a client - Only admins
     */
    public function destroy($id)
    {
        $client = Client::find($id);
        
        if (!$client) {
            return $this->error('Client not found', null, 404);
        }
        
        $this->authorize('delete', $client);
        $client->delete();
        return $this->success(null, 'Client deleted successfully');
    }
}
