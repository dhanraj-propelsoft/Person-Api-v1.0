<?php

use App\Http\Controllers\Api\v1\Controller\Person\PersonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::post('personDatas', [PersonController::class, 'personDatas'])->name('personDatas');
Route::post('storeTempPerson',[PersonController::class,'storeTempPerson'])->name('storeTempPerson');
Route::post('findCredential', [PersonController::class,'findCredential'])->name('findCredential');
Route::post('personOtpValidation',[PersonController::class,'personOtpValidation'])->name('personOtpValidation');
Route::post('generateEmailOtp', [PersonController::class, 'generateEmailOtp'])->name('generateEmailOtp');