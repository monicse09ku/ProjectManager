<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\Client;
use App\Http\Requests\ProjectRequest;
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

    public function store(ProjectRequest $request)
    {
        $validated = $request->validated();

        // generate slug from title and ensure uniqueness
        $slugGenerator = new SlugGenerator();
        $validated['slug'] = $slugGenerator->generate(Project::class, $validated['title']);

        Project::create($validated);

        return back()->with('success', 'Project created successfully.');
    }

    public function update(ProjectRequest $request, Project $project)
    {
        $validated = $request->validated();

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
