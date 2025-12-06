<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TaskResource;
use App\Http\Traits\ApiResponse;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use ApiResponse;

    /**
     * Get all projects
     */
    public function index(Request $request)
    {
        $projects = Project::with('client')->get();
        return $this->success(ProjectResource::collection($projects), 'Projects retrieved successfully');
    }

    /**
     * Get tasks for a specific project
     */
    public function tasks($id)
    {
        $project = Project::findOrFail($id);
        $tasks = $project->tasks()->get();
        return $this->success(TaskResource::collection($tasks), 'Project tasks retrieved successfully');
    }
}
