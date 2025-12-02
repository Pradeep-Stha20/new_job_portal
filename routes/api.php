<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API endpoint to get application details - use auth middleware (supports both session and sanctum)
Route::middleware('auth')->get('/applications/{id}', function (Request $request, $id) {
    $application = JobApplication::with(['user', 'job'])->find($id);
    
    if (!$application) {
        return response()->json(['error' => 'Application not found'], 404);
    }
    
    // Check if user owns the job
    if ($application->job->user_id != Auth::user()->id) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    
    return response()->json($application);
});
