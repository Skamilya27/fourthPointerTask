<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('folders', [FileManagerController::class, 'createFolder']);
    Route::post('folders/{folder}/subfolders', [FileManagerController::class, 'createSubfolder']);
    Route::post('folders/{folder}/files', [FileManagerController::class, 'uploadFile']);
    Route::get('folders/{folder}/files', [FileManagerController::class, 'listFiles']);
    Route::patch('files/{file}', [FileManagerController::class, 'updateFile']);
    Route::delete('files/{file}', [FileManagerController::class, 'deleteFile']);
    Route::get('search', [FileManagerController::class, 'search']);
    // Route::post('share', [FileManagerController::class, 'share']);
});
