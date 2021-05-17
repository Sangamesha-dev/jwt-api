<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DataController;


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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('register', [UserController::class,'register']);
Route::post('login', [UserController::class,'authenticate']);
Route::get('open', [DataController::class,'open']);

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('user', [UserController::class,'getAuthenticatedUser']);
    Route::get('users', [UserController::class,'getAllUsers']);
    Route::get('users/{id}', [UserController::class,'getUser']);
    Route::put('user/{id}',[UserController::class,'updateUser']);
    Route::delete('users/{id}',[UserController::class,'deleteUser']);

    Route::get('closed', [DataController::class,'closed']);

    Route::post('users/bulkupload', [UserController::class,'bulkUpload']);

});
