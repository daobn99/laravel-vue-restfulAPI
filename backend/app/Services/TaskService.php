<?php

namespace App\Services;

use App\Models\Task;

class TaskService
{
    public function create(array $data): Task
    {
        return Task::create($data);
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
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $column = ltrim($sort, '-');

        // whitelist sort
        if (!in_array($column, self::ALLOWED_SORTS, true)) {
            $column = 'created_at';
        }

        return Task::query()
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
            ->when($filters['priority'] ?? null, fn($q, $priority) => $q->where('priority', $priority))
            ->when($filters['title'] ?? null, fn($q, $title) => $q->where('title', 'like', "%{$title}%"))
            ->orderBy($column, $direction)
            ->paginate($perPage);
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
