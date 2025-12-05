<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\Client;
use Illuminate\Support\Str;
use App\Services\SlugGenerator;

class ProjectsController extends Controller
{
    public function index()
    {
        $projects = Project::with('client')->get();
        $clients = Client::all();

        return Inertia::render('projects/Index', [
            'projects' => $projects,
            'clients' => $clients,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string',
        ]);

        // generate slug from title and ensure uniqueness
        $slugGenerator = new SlugGenerator();
        $validated['slug'] = $slugGenerator->generate(Project::class, $validated['title']);

        $project = Project::create($validated);

        return back()->with('success', 'Project created successfully.');
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|string',
        ]);

        // generate slug from title and ensure uniqueness excluding current project
        $slugGenerator = new SlugGenerator();
        $validated['slug'] = $slugGenerator->generate(Project::class, $validated['title'], $project->id);

        $project->update($validated);

        return back()->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return back()->with('success', 'Project deleted successfully.');
    }
}
