<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\AttachTokenFromCookie;
use Illuminate\Support\Facades\Route;

Route::post('/register',[UserController::class,'register']);
Route::post('/login',[UserController::class,'login']);


Route::middleware(['auth:sanctum'])->group(function(){
   Route::get('/user',[UserController::class,'getAuthenticatedUser']);
   Route::get('/projects',[ProjectController::class,'index']);
   Route::post('/projects',[ProjectController::class,'store']);

   Route::get('/project/{project}',[CategoryController::class,'index']);
   Route::get('/category/{category}',[TaskController::class,'index']);

   Route::put('/user/{id}',[UserController::class,'update']);

   Route::middleware([\App\Http\Middleware\CheckPermission::class])->group(function(){

       // Edit & Delete Project
       Route::put('/projects/{project}',[ProjectController::class,'update']);
       Route::delete('/projects/{project}',[ProjectController::class,'destroy']);

       // Add & Edit & Delete Category
       Route::post('/category/{project}',[CategoryController::class,'store']);
       Route::put('/category/{category}',[CategoryController::class,'update']);
       Route::delete('/category/{category}',[CategoryController::class,'destroy']);

       // Add & Edit & Delete Tasks
       Route::post('/tasks/{category}',[TaskController::class,'store']);
       Route::delete('/tasks/{task}',[TaskController::class,'destroy']);
       Route::put('/tasks/position/{task}',[TaskController::class,'updatePosition']);
       Route::put('/tasks/title/{task}',[TaskController::class,'updateName']);
       Route::put('/tasks/completed/{task}',[TaskController::class,'updateCompleted']);
   });

   Route::post('/logout',[UserController::class,'logout']);
});
