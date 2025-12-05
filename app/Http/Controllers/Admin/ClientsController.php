<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Client;

class ClientsController extends Controller
{
    public function index()
    {
        $clients = Client::all();
        return Inertia::render('clients/Index', [
            'clients' => $clients,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255|unique:clients',
        ]);

        $client = Client::create($validated);

        return back()->with('success', 'Client created successfully.');
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255|unique:clients,client_name,' . $client->id,
        ]);

        $client->update($validated);

        return back()->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return back()->with('success', 'Client deleted successfully.');
    }
}
