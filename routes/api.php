<?php

use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\ProjectController;
use App\Http\Controllers\admin\ServiceController;
use App\Http\Controllers\frontend\ServiceController as FrontServiceController;
use App\Http\Controllers\AuthenticationController;
use Illuminate\Support\Facades\Route;


//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('authenticate',[AuthenticationController::class,'authenticate']);
Route::get('get-services',[FrontServiceController::class,'index']);
Route::get('get-latest-services',[FrontServiceController::class,'latestServices']);

Route::group(['middleware'=>['auth:sanctum']],function (){
    Route::get('dashboard',[DashboardController::class,'index']);
    Route::get('logout',[AuthenticationController::class,'logout']);

    // service
    Route::get('services',[ServiceController::class,'index']);
    Route::post('services',[ServiceController::class,'store']);
    Route::put('services/{id}',[ServiceController::class,'update']);
    Route::get('services/{id}',[ServiceController::class,'show']);
    Route::delete('services/{id}',[ServiceController::class,'destroy']);

    // project
    Route::get('projects',[ProjectController::class,'index']);
    Route::post('projects',[ProjectController::class,'store']);
    Route::put('projects/{id}',[ProjectController::class,'update']);
    Route::delete('projects/{id}',[ProjectController::class,'destroy']);

});
