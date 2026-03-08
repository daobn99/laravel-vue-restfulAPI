<?php

namespace App\Services;

use App\Models\Task;

class TaskService
{
    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function paginate(int $perPage = 10)
    {
        // latest() = order by created_at DESC
        return Task::latest()->paginate($perPage);
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
