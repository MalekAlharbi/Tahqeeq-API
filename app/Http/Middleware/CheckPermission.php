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
        $userId = auth()->user()->id;

        if ($request->route('project')){
            $project = $request->route('project');
        }elseif ($request->route('category')){
            $project = $request->route('category')->project;
        }elseif ($request->route('task')){
            $project = $request->route('task')->category->project;
        }

        $role = $project->users()->find($userId)->pivot->role;

        if ($role !== 'owner') {
            return response()->json([
               "message" => "You can't access this project"
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
