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
Route::post('storePerson', [PersonController::class, 'storePerson'])->name('storePerson');
Route::post('checkMemberOrPerson', [PersonController::class, 'checkMemberOrPerson'])->name('checkMemberOrPerson');
Route::post('checkPersonEmail', [PersonController::class, 'checkPersonEmail'])->name('checkPersonEmail');
Route::post('personMobileOtp', [PersonController::class, 'personMobileOtp'])->name('personMobileOtp');
Route::post('personUpdate', [PersonController::class, 'personUpdate'])->name('personUpdate');
Route::post('personToMember', [PersonController::class, 'personToMember'])->name('personToMember');
Route::post('emailOtpValidation', [PersonController::class, 'emailOtpValidation'])->name('emailOtpValidation');
Route::post('personProfiles', [PersonController::class, 'personProfiles'])->name('personProfiles');
Route::post('profileUpdate', [PersonController::class, 'profileUpdate'])->name('profileUpdate');
Route::post('getPersonAllDetails', [PersonController::class, 'getPersonAllDetails'])->name('getPersonAllDetails');
Route::post('memberAllDetails', [PersonController::class, 'memberAllDetails'])->name('memberAllDetails');
Route::post('addSecondaryMobile', [PersonController::class, 'addSecondaryMobile'])->name('addSecondaryMobile');
Route::post('resendOtpForMobile', [PersonController::class, 'resendOtpForMobile'])->name('resendOtpForMobile');
Route::post('deleteForMobileNoByUid', [PersonController::class, 'deleteForMobileNoByUid'])->name('deleteForMobileNoByUid');
Route::post('makeAsPrimaryMobileOtpValidate', [PersonController::class, 'makeAsPrimaryMobileOtpValidate'])->name('makeAsPrimaryMobileOtpValidate');
Route::post('addSecondaryEmail', [PersonController::class, 'addSecondaryEmail'])->name('addSecondaryEmail');
Route::post('resendOtpForEmail', [PersonController::class, 'resendOtpForEmail'])->name('resendOtpForEmail');
Route::post('deleteForEmailByUid', [PersonController::class, 'deleteForEmailByUid'])->name('deleteForEmailByUid');
Route::post('makeAsPrimaryEmailOtpValidate', [PersonController::class, 'makeAsPrimaryEmailOtpValidate'])->name('makeAsPrimaryEmailOtpValidate');
Route::post('resendOtpForSecondaryMobile', [PersonController::class, 'resendOtpForSecondaryMobile'])->name('resendOtpForSecondaryMobile');
Route::post('resendOtpForSecondaryEmail', [PersonController::class, 'resendOtpForSecondaryEmail'])->name('resendOtpForSecondaryEmail');
Route::post('OtpValidateSecondaryMobileNo', [PersonController::class, 'OtpValidateSecondaryMobileNo'])->name('OtpValidateSecondaryMobileNo');
Route::post('OtpValidateForSecondaryEmail', [PersonController::class, 'OtpValidateForSecondaryEmail'])->name('OtpValidateForSecondaryEmail');
Route::post('otpValidationForMobile', [PersonController::class, 'otpValidationForMobile'])->name('otpValidationForMobile');
