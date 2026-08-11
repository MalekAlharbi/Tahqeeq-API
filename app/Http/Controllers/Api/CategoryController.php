<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Project;
use App\Traits\ApiResponseTrait;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        $categories = $project->categories()
            ->orderBy('position')
            ->with(['tasks' => fn($query) => $query->orderBy('position')])
            ->get();

        return $this->successResponse([
            'categories' => CategoryResource::collection($categories)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request, Project $project)
    {
        $next = $project->categories()->max('position') + 1;
        $validatedData = $request->validated();

        $validatedData['user_id'] = auth()->id();
        $validatedData['position'] = $next;

        $category = $project->categories()->create($validatedData);

        return $this->successResponse([
            'category' => new CategoryResource($category)
        ], 'Category created successfully', 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $validatedData = $request->validated();

        $oldPosition = $category->position;
        $newPosition = $validatedData['position'] ?? null;

        if ($newPosition !== null && $oldPosition !== $newPosition) {
            if ($newPosition < $oldPosition) {
                Category::where('project_id', $category->project_id)
                    ->where('position', '>=', $newPosition)
                    ->where('position', '<', $oldPosition)
                    ->increment('position');
            } else {
                Category::where('project_id', $category->project_id)
                    ->where('position', '>', $oldPosition)
                    ->where('position', '<=', $newPosition)
                    ->decrement('position');
            }
        }

        $category->update($validatedData);

        return $this->successResponse([
            'category' => new CategoryResource($category->fresh())
        ], 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $projectId = $category->project_id;
        $position = $category->position;

        $category->delete();

        Category::where('project_id', $projectId)
            ->where('position', '>', $position)
            ->decrement('position');

        return $this->successResponse(null, 'Category deleted successfully');
    }
}
