<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TaskResource;
use App\Http\Traits\ApiResponse;
use App\Models\Project;
use App\Http\Requests\ProjectRequest;
use App\Services\SlugGenerator;

class ProjectController extends Controller
{
    use ApiResponse;

    /**
     * Get all projects - Only admins can view
     */
    public function index()
    {
        $this->authorize('viewAny', Project::class);

        $projects = Project::with('client')->get();
        return $this->success(ProjectResource::collection($projects), 'Projects retrieved successfully');
    }

    /**
     * Show a single project - Only admins can view
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        return $this->success(new ProjectResource($project->load('client')), 'Project retrieved successfully');
    }

    /**
     * Create a project - Only admins can create
     */
    public function store(ProjectRequest $request)
    {
        $this->authorize('create', Project::class);

        $validated = $request->validated();
        $validated['slug'] = (new SlugGenerator())->generate(Project::class, $validated['title']);

        $project = Project::create($validated);

        return $this->success(new ProjectResource($project->load('client')), 'Project created successfully', 201);
    }

    /**
     * Update a project - Only admins can update
     */
    public function update(ProjectRequest $request, $id)
    {
        $project = Project::find($id);
        
        if (!$project) {
            return $this->error('Project not found', null, 404);
        }
        
        $this->authorize('update', $project);

        $validated = $request->validated();
        $validated['slug'] = (new SlugGenerator())->generate(Project::class, $validated['title'], $project->id);

        $project->update($validated);

        return $this->success(new ProjectResource($project->load('client')), 'Project updated successfully');
    }

    /**
     * Delete a project - Only admins can delete
     */
    public function destroy($id)
    {
        $project = Project::find($id);
        
        if (!$project) {
            return $this->error('Project not found', null, 404);
        }
        
        $this->authorize('delete', $project);

        $project->delete();

        return $this->success(null, 'Project deleted successfully');
    }

    /**
     * Get tasks for a specific project - Only admins can view
     */
    public function tasks(Project $project)
    {
        $this->authorize('view', $project);

        $tasks = $project->tasks()->get();
        return $this->success(TaskResource::collection($tasks), 'Project tasks retrieved successfully');
    }
}
