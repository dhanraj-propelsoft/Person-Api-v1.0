<?php

namespace App\Http\Controllers\Api\v1\Controller\Person;

use App\Http\Controllers\Api\v1\Service\Person\PersonService;
use App\Http\Controllers\Api\v1\Service\Common\CommonService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PersonController extends Controller
{
    protected $personService;
    public function __construct(PersonService $personService,CommonService $CommonService)
    {
        $this->personService = $personService;
        $this->CommonService = $CommonService;
    }
    public function findCredential(Request $request): JsonResponse
    {
        Log::info('PersonController > findCredential function Inside.' . json_encode($request->all()));
        $response = $this->personService->findCredential($request->all());
        Log::info('PersonController > findCredential function Return.' . json_encode($response));
        return $response;
    }

    public function findMobileNumber(Request $request): JsonResponse
    {

        Log::info('PersonController > findMobileNumber function Inside.' . json_encode($request->all()));
        $response = $this->personService->findMobileNumber($request->all());
        Log::info('PersonController > findMobileNumber function Return.' . json_encode($response));
        return $response;
    }
    public function storePerson(Request $request)
    {

        Log::info('PersonController > storePerson function Inside.' . json_encode($request->all()));
        $response = $this->personService->storePerson($request->all());
        Log::info('PersonController > storePerson function Return.' . json_encode($response));
        return $response;
    }
    public function storeTempPerson(Request $request): JsonResponse
    {

        Log::info('PersonController > storeTempPerson function Inside.' . json_encode($request->all()));
        $response = $this->personService->storeTempPerson($request->all());
        Log::info('PersonController > storeTempPerson function Return.' . json_encode($response));
        return $response;
    }
    public function tempPersonOtpValidate(Request $request): JsonResponse
    {
        Log::info('PersonController > tempPersonOtpValidate function Inside.' . json_encode($request->all()));
        $response = $this->personService->tempPersonOtpValidate($request->all());
        Log::info('PersonController > tempPersonOtpValidate function Return.' . json_encode($response));
        return $response;
    }
    public function generateEmailOtp(Request $request)
    {
        Log::info('PersonController > mobileOtpValidated function Inside.' . json_encode($request->all()));
        $response = $this->personService->generateEmailOtp($request->all());
        Log::info('PersonController > mobileOtpValidated function Return.' . json_encode($response));
        return $response;
    }
    public function checkMemberOrPerson(Request $request)
    {
        Log::info('PersonController > checkMemberOrPerson function Inside.' . json_encode($request->all()));
        $response = $this->personService->checkMemberOrPerson($request->all());
        Log::info('PersonController > checkMemberOrPerson function Return.' . json_encode($response));
        return $response;
    }
    public function checkPersonEmail(Request $request)
    {
        Log::info('PersonController > checkPersonEmail function Inside.' . json_encode($request->all()));
        $response = $this->personService->checkPersonEmail($request->all());
        Log::info('PersonController > checkPersonEmail function Return.' . json_encode($response));
        return $response;
    }
    public function personMobileOtp(Request $request)
    {
        Log::info('PersonController > personMobileOtp function Inside.' . json_encode($request->all()));
        $response = $this->personService->personMobileOtp($request->all());
        Log::info('PersonController > personMobileOtp function Return.' . json_encode($response));
        return $response;
    }
    public function personDatas(Request $request): JsonResponse
    {

        Log::info('PersonController > personDatas function Inside.' . json_encode($request->all()));
        $response = $this->personService->personDatas($request->all());
        Log::info('PersonController > personDatas function Return.' . json_encode($response));
        return $response;
    }
    public function personUpdate(Request $request)
    {
        Log::info('PersonController > personUpdate function Inside.' . json_encode($request->all()));
        $response = $this->personService->personUpdate($request->all());
        Log::info('PersonController > personUpdate function Return.' . json_encode($response));
        return $response;
    }
    public function personToMember(Request $request)
    {
        Log::info('PersonController > personToMember function Inside.' . json_encode($request->all()));
        $response = $this->personService->personToMember($request->all());
        Log::info('PersonController > personToMember function Return.' . json_encode($response));
        return $response;
    }

    public function emailOtpValidation(Request $request)
    {
        Log::info('PersonController > emailOtpValidation function Inside.' . json_encode($request->all()));
        $response = $this->personService->emailOtpValidation($request->all());
        Log::info('PersonController > emailOtpValidation function Return.' . json_encode($response));
        return $response;
    }
    public function personProfiles(Request $request)
    {
        Log::info('PersonController > personProfiles function Inside.' . json_encode($request->all()));
        $response = $this->personService->personProfileDetails($request->all());
        Log::info('PersonController > personProfiles function Return.' . json_encode($response));
        return $response;
    }
    public function profileUpdate(Request $request)
    {
        Log::info('PersonController > profileUpdate function Inside.' . json_encode($request->all()));
        $response = $this->personService->storePerson($request->all());
        Log::info('PersonController > profileUpdate function Return.' . json_encode($response));
        return $response;
    }
    public function getPersonAllDetails(Request $request)
    {
        Log::info('PersonController > getPersonAllDetails function Inside.' . json_encode($request->all()));
        $response = $this->personService->getPersonAllDetails($request->all());
        Log::info('PersonController > getPersonAllDetails function Return.' . json_encode($response));
        return $response;
    }
    public function memberAllDetails(Request $request)
    {
        Log::info('PersonController > memberAllDetails function Inside.' . json_encode($request->all()));
        $response = $this->personService->memberAllDetails($request->all());
        Log::info('PersonController > memberAllDetails function Return.' . json_encode($response));
        return $response;
    }
    public function addSecondaryMobile(Request $request)
    {
        Log::info('PersonController > addSecondaryMobile function Inside.' . json_encode($request->all()));
        $response = $this->personService->addSecondaryMobile($request->all());
        Log::info('PersonController > addSecondaryMobile function Return.' . json_encode($response));
        return $response;
    }
    public function resendOtpForMobile(Request $request)
    {
        Log::info('PersonController > resendOtpForMobile function Inside.' . json_encode($request->all()));
        $response = $this->personService->resendOtpForMobile($request->all());
        Log::info('PersonController > resendOtpForMobile function Return.' . json_encode($response));
        return $response;
    }
    public function deleteForMobileNoByUid(Request $request)
    {
        Log::info('PersonController > deleteForMobileNoByUid function Inside.' . json_encode($request->all()));
        $response = $this->personService->deleteForMobileNoByUid($request->all());
        Log::info('PersonController > deleteForMobileNoByUid function Return.' . json_encode($response));
        return $response;
    }
    public function makeAsPrimaryMobileOtpValidate(Request $request)
    {
        Log::info('PersonController > makeAsPrimaryMobileOtpValidate function Inside.' . json_encode($request->all()));
        $response = $this->personService->makeAsPrimaryMobileOtpValidate($request->all());
        Log::info('PersonController > makeAsPrimaryMobileOtpValidate function Return.' . json_encode($response));
        return $response;
    }
    public function addSecondaryEmail(Request $request)
    {
        Log::info('PersonController > addSecondaryEmail function Inside.' . json_encode($request->all()));
        $response = $this->personService->addSecondaryEmail($request->all());
        Log::info('PersonController > addSecondaryEmail function Return.' . json_encode($response));
        return $response;
    }
    public function resendOtpForEmail(Request $request)
    {
        Log::info('PersonController > resendOtpForEmail function Inside.' . json_encode($request->all()));
        $response = $this->personService->resendOtpForEmail($request->all());
        Log::info('PersonController > resendOtpForEmail function Return.' . json_encode($response));
        return $response;
    }
    public function deleteForEmailByUid(Request $request)
    {
        Log::info('PersonController > deleteForEmailByUid function Inside.' . json_encode($request->all()));
        $response = $this->personService->deleteForEmailByUid($request->all());
        Log::info('PersonController > deleteForEmailByUid function Return.' . json_encode($response));
        return $response;
    }

    public function makeAsPrimaryEmailOtpValidate(Request $request)
    {
        Log::info('PersonController > makeAsPrimaryEmailOtpValidate function Inside.' . json_encode($request->all()));
        $response = $this->personService->makeAsPrimaryEmailOtpValidate($request->all());
        Log::info('PersonController > makeAsPrimaryEmailOtpValidate function Return.' . json_encode($response));
        return $response;

    }
    public function resendOtpForSecondaryMobile(Request $request)
    {
        Log::info('PersonController > resendOtpForSecondaryMobile function Inside.' . json_encode($request->all()));
        $response = $this->personService->resendOtpForSecondaryMobile($request->all());
        Log::info('PersonController > resendOtpForSecondaryMobile function Return.' . json_encode($response));
        return $response;
    }
    public function resendOtpForSecondaryEmail(Request $request)
    {
        Log::info('PersonController > resendOtpForSecondaryEmail function Inside.' . json_encode($request->all()));
        $response = $this->personService->resendOtpForSecondaryEmail($request->all());
        Log::info('PersonController > resendOtpForSecondaryEmail function Return.' . json_encode($response));
        return $response;
    }
    public function OtpValidateSecondaryMobileNo(Request $request)
    {
        Log::info('PersonController > OtpValidateSecondaryMobileNo function Inside.' . json_encode($request->all()));
        $response = $this->personService->OtpValidateSecondaryMobileNo($request->all());
        Log::info('PersonController > OtpValidateSecondaryMobileNo function Return.' . json_encode($response));
        return $response;
    }
    public function OtpValidateForSecondaryEmail(Request $request)
    {
        Log::info('PersonController > OtpValidateForSecondaryEmail function Inside.' . json_encode($request->all()));
        $response = $this->personService->OtpValidateForSecondaryEmail($request->all());
        Log::info('PersonController > OtpValidateForSecondaryEmail function Return.' . json_encode($response));
        return $response;
    }
    public function otpValidationForMobile(Request $request)
    {
        Log::info('PersonController > otpValidationForMobile function Inside.' . json_encode($request->all()));
        $response = $this->personService->otpValidationForMobile($request->all());
        Log::info('PersonController > otpValidationForMobile function Return.' . json_encode($response));
        return $response;
    }
    public function findExactPersonWithEmailAndMobile(Request $request)
    {
        Log::info('PersonController > findExactPersonWithEmailAndMobile function Inside.' . json_encode($request->all()));
        $response = $this->personService->findExactPersonWithEmailAndMobile($request->all());
        Log::info('PersonController > findExactPersonWithEmailAndMobile function Return.' . json_encode($response));
        return $response;
    }
    public function findMemberDataByUid(Request $request)
    {
        Log::info('PersonController > findMemberDataByUid function Inside.' . json_encode($request->all()));
        $response = $this->personService->findMemberDataByUid($request->all());
        Log::info('PersonController > findMemberDataByUid function Return.' . json_encode($response));
        return $response;
    }
    public function getPrimaryMobileAndEmailbyUid(Request $request)
    {
        Log::info('PersonController > getPrimaryMobileAndEmailbyUid function Inside.' . json_encode($request->all()));
        $response = $this->personService->getPrimaryMobileAndEmailbyUid($request->all());
        Log::info('PersonController > getPrimaryMobileAndEmailbyUid function Return.' . json_encode($response));
        return $response;
    }
    public function personProfileDatas(Request $request)
    {
        Log::info('PersonController > personProfileDatas function Inside.' . json_encode($request->all()));
        $response = $this->personService->personProfileDatas($request->all());
        Log::info('PersonController > personProfileDatas function Return.' . json_encode($response));
        return $response;
    }
    public function getPersonMasterData()
    {
        $response = $this->CommonService->getPersonMasterData();
        Log::info('PersonController > getPersonMasterData function Return.' . json_encode($response));
        return $this->CommonService->sendResponse($response,true);
    }
    public function getPersonMobileNoByUid(Request $request)
    {
        Log::info('PersonController > getPersonMobileNoByUid function Inside.' . json_encode($request->all()));
        $response = $this->personService->getPersonMobileNoByUid($request->all());
        Log::info('PersonController > getPersonMobileNoByUid function Return.' . json_encode($response));
        return $this->CommonService->sendResponse($response,true);
    }
    public function getPersonPrimaryDataByUid(Request $request)
    {
        Log::info('PersonController > getPersonPrimaryDataByUid function Inside.' . json_encode($request->all()));
        $response = $this->personService->getPersonPrimaryDataByUid($request->all());
        Log::info('PersonController > getPersonPrimaryDataByUid function Return.' . json_encode($response));
        return $this->CommonService->sendResponse($response,true);
    }
    public function personMotherTongueByUid(Request $request)
    {
        Log::info('PersonController > personMotherTongueByUid function Inside.' . json_encode($request->all()));
        $response = $this->personService->personMotherTongueByUid($request->all());
        Log::info('PersonController > personMotherTongueByUid function Return.' . json_encode($response));
        return $this->CommonService->sendResponse($response,true);
    }
    public function personGetAnniversaryDate(Request $request)
    {
        Log::info('PersonController > personGetAnniversaryDate function Inside.' . json_encode($request->all()));
        $response = $this->personService->personGetAnniversaryDate($request->all());
        Log::info('PersonController > personGetAnniversaryDate function Return.' . json_encode($response));
        return $this->CommonService->sendResponse($response,true);
    }

    public function personAddressByUid(Request $request)
    {
        Log::info('PersonController > personAddressByUid function Inside.' . json_encode($request->all()));
        $response = $this->personService->personAddressByUid($request->all());
        Log::info('PersonController > personAddressByUid function Return.' . json_encode($response));
        return $this->CommonService->sendResponse($response,true);
    }
    public function getPersonEmailByUidAndEmail(Request $request)
    {
        Log::info('PersonController > getPersonEmailByUidAndEmail function Inside.' . json_encode($request->all()));
        $response = $this->personService->getPersonEmailByUidAndEmail($request->all());
        Log::info('PersonController > getPersonEmailByUidAndEmail function Return.' . json_encode($response));
        return $this->CommonService->sendResponse($response,true);
    }
    public function resendOtpForTempPerson($tempId)
    {
        Log::info('PersonController > resendOtpForTempPerson function Inside.' . json_encode($tempId));
        $response = $this->personService->resendOtpForTempPerson($tempId);
        Log::info('PersonController > resendOtpForTempPerson function Return.' . json_encode($response));
        return $this->CommonService->sendResponse($response,true);
    }
}

