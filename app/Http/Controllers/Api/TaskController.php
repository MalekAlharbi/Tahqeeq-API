<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskNameRequest;
use App\Http\Requests\Task\UpdateTaskPositionRequest;
use App\Http\Resources\TaskResource;
use App\Models\Category;
use App\Models\Task;
use App\Traits\ApiResponseTrait;

class TaskController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Category $category)
    {
        return $this->successResponse([
            'tasks' => TaskResource::collection($category->tasks()->orderBy('position')->get())
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request, Category $category)
    {
        $next = $category->tasks()->max('position') + 1;
        $validatedData = $request->validated();

        $validatedData['user_id'] = auth()->id();
        $validatedData['position'] = $next;

        $task = $category->tasks()->create($validatedData);

        return $this->successResponse([
            'task' => new TaskResource($task)
        ], 'Task created successfully', 201);
    }

    /**
     * Update title of the task.
     */
    public function updateName(UpdateTaskNameRequest $request, Task $task)
    {
        $validatedData = $request->validated();
        $task->update($validatedData);

        return $this->successResponse([
            'task' => new TaskResource($task->fresh())
        ], 'Task title updated successfully');
    }

    /**
     * Update position and category of the task (Kanban drag-and-drop).
     */
    public function updatePosition(UpdateTaskPositionRequest $request, Task $task)
    {
        $validatedData = $request->validated();

        $oldCategoryId = $task->category_id;
        $newCategoryId = (int)$validatedData['category_id'];

        $oldPosition = $task->position;
        $newPosition = (int)$validatedData['position'];

        if ($oldCategoryId === $newCategoryId) {
            if ($oldPosition !== $newPosition) {
                if ($newPosition < $oldPosition) {
                    Task::where('category_id', $oldCategoryId)
                        ->where('position', '>=', $newPosition)
                        ->where('position', '<', $oldPosition)
                        ->increment('position');
                } else {
                    Task::where('category_id', $oldCategoryId)
                        ->where('position', '>', $oldPosition)
                        ->where('position', '<=', $newPosition)
                        ->decrement('position');
                }
            }
        } else {
            // Task moved to a different category list
            Task::where('category_id', $oldCategoryId)
                ->where('position', '>', $oldPosition)
                ->decrement('position');

            Task::where('category_id', $newCategoryId)
                ->where('position', '>=', $newPosition)
                ->increment('position');
        }

        $task->update($validatedData);

        return $this->successResponse([
            'task' => new TaskResource($task->fresh())
        ], 'Task position updated successfully');
    }

    /**
     * Toggle task completion status.
     */
    public function updateCompleted(Task $task)
    {
        $task->update([
            'is_completed' => !$task->is_completed
        ]);

        return $this->successResponse([
            'task' => new TaskResource($task->fresh())
        ], 'Task completion status updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $categoryId = $task->category_id;
        $position = $task->position;

        $task->delete();

        Task::where('category_id', $categoryId)
            ->where('position', '>', $position)
            ->decrement('position');

        return $this->successResponse(null, 'Task deleted successfully');
    }
}
