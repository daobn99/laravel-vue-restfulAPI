<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class TaskService
{
    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function createForProject(Project $project, array $data): Task
    {
        return $project->tasks()->create($data);
    }

    // public function paginate(array $filters = [], int $perPage = 10)
    // {
    //     $query = Task::query();
    //     if (!empty($filters['status'])) {
    //         $query->where('status', $filters['status']);
    //     }

    //     // check bên trong hàm when() để hiểu $q, $priority được truyền từ đâu
    //     $query->when($filters['priority'] ?? null, function ($q, $priority) {
    //         $q->where('priority', $priority);
    //     });

    //     if (!empty($filters['title'])) {
    //         // WHERE title LIKE '%abcd%'
    //         $query->where('title', 'like', '%' . $filters['title'] . '%');
    //     }
    //     // latest() = order by created_at DESC
    //     return $query->latest()->paginate($perPage);
    // }
    private const ALLOWED_SORTS = ['created_at', 'priority', 'title', 'status'];

    public function paginate(array $filters = [], int $perPage = 10, string $sort = '-created_at')
    {
        $query = Task::query();
        $query = $this->applyFilters($query, $filters);
        $query = $this->applySort($query, $sort);

        return $query
            ->with('project.user')
            ->paginate($perPage);
    }

    public function paginateForProject(
        Project $project,
        array $filters = [],
        int $perPage = 10,
        string $sort = '-created_at'
    ) {
        $query = $project->tasks();
        $query = $this->applyFilters($query, $filters);
        $query = $this->applySort($query, $sort);

        return $query
            ->with('project.user')
            ->paginate($perPage);
    }

    private function applyFilters(Builder|Relation $query, array $filters): Builder|Relation
    {
        return $query
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
            ->when($filters['priority'] ?? null, fn($q, $priority) => $q->where('priority', $priority))
            ->when($filters['title'] ?? null, fn($q, $title) => $q->where('title', 'like', "%{$title}%"));
    }

    private function applySort(Builder|Relation $query, string $sort): Builder|Relation
    {
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $column = ltrim($sort, '-');

        if (!in_array($column, self::ALLOWED_SORTS, true)) {
            $column = 'created_at';
        }
        return $query->orderBy($column, $direction);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task->refresh();
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }
}
