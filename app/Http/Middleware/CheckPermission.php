<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(["message" => "Unauthenticated"], Response::HTTP_UNAUTHORIZED);
        }

        $project = null;
        if ($request->route('project')) {
            $project = $request->route('project');
        } elseif ($request->route('category')) {
            $project = $request->route('category')->project;
        } elseif ($request->route('task')) {
            $project = $request->route('task')->category->project;
        }

        if (!$project) {
            return $next($request);
        }

        $projectUser = $project->users()->find($user->id);

        if (!$projectUser) {
            return response()->json([
                "message" => "You do not have access to this project"
            ], Response::HTTP_FORBIDDEN);
        }

        $role = $projectUser->pivot->role;

        // Viewers are not allowed to perform write/update/delete operations
        if ($role === 'viewer' && !$request->isMethod('get')) {
            return response()->json([
                "message" => "Viewers are not authorized to modify project resources"
            ], Response::HTTP_FORBIDDEN);
        }

        // Only project owners can delete the project
        if ($request->isMethod('delete') && $request->route('project') && $role !== 'owner') {
            return response()->json([
                "message" => "Only the project owner can delete this project"
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
