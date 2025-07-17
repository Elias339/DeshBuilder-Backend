<?php

use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\ServiceController;
use App\Http\Controllers\admin\TempImageController;
use App\Http\Controllers\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('authenticate',[AuthenticationController::class,'authenticate']);


Route::group(['middleware'=>['auth:sanctum']],function (){
    Route::get('dashboard',[DashboardController::class,'index']);
    Route::get('logout',[AuthenticationController::class,'logout']);

    Route::get('services',[ServiceController::class,'index']);
    Route::post('services',[ServiceController::class,'store']);
    Route::put('services/{id}',[ServiceController::class,'update']);
    Route::get('services/{id}',[ServiceController::class,'show']);
    Route::delete('services/{id}',[ServiceController::class,'destroy']);

    Route::post('temp-images',[TempImageController::class,'store']);
});
