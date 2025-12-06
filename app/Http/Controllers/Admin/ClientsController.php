<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\Client;
use App\Http\Requests\ClientRequest;

class ClientsController extends Controller
{
    public function index()
    {
        $clients = Client::all();
        return Inertia::render('clients/Index', [
            'clients' => $clients,
        ]);
    }

    public function store(ClientRequest $request)
    {
        Client::create($request->validated());
        return back()->with('success', 'Client created successfully.');
    }

    public function update(ClientRequest $request, Client $client)
    {
        $client->update($request->validated());
        return back()->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return back()->with('success', 'Client deleted successfully.');
    }
}
