<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController as FortifyAuthenticatedSessionController;
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

Route::group(['middleware' => 'auth:sanctum'], function() {
// Authentication routes
Route::prefix('auth')->withoutMiddleware('auth:sanctum')->group(function () { 
$limiter =config('fortify.limiters.login');
Route::POST('/Login', [App\Http\Controllers\Api\Auth\LoginController::class, 'login'])
->middleware(array_filter([
    'guest:'.config('fortify.guard'),
    $limiter? 'throttle: '.$limiter: null,

]));
Route::POST('/Register', [App\Http\Controllers\Api\Auth\LoginController::class, 'index'])->middleware( 'guest:'.config('fortify.guard')); 
});
Route::POST('/logout', [App\Http\Controllers\Api\Auth\LoginController::class, 'destroy']);
Route::POST('/createCompany', [App\Http\Controllers\Api\CompanyController::class, 'index']); 
Route::POST('/SwitchCompany', [App\Http\Controllers\Api\CompanyController::class, 'create']);
Route::POST('/updateCompany', [App\Http\Controllers\Api\CompanyController::class, 'edit']);
Route::get('/listCompany', [App\Http\Controllers\Api\CompanyController::class, 'show']);
Route::POST('/deleteCompany', [App\Http\Controllers\Api\CompanyController::class, 'destroy']);
});





