<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\TaskIndexRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Services\TaskService;

class ProjectTaskController extends Controller
{
    private const RELATIONS_PROJECT = 'project.user';

    public function __construct(
        protected TaskService $taskService
    ) {}


    /**
     * Display a listing of the resource.
     */
    public function index(Project $project, TaskIndexRequest $request)
    {
        $filters = $request->only(['title', 'status', 'priority']);
        $perPage = $request->integer('per_page', 10);
        $sort = $request->query('sort', '-created_at');
        $tasks = $this->taskService->paginateForProject($project, $filters, $perPage, $sort);
        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Project $project, StoreTaskRequest $request)
    {
        $task = $this->taskService->createForProject($project, $request->validated());
        $task->load(self::RELATIONS_PROJECT);
        return new TaskResource($task);
    }
}
