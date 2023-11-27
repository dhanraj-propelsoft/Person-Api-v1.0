<?php

namespace App\Http\Controllers\Api\v1\Controller\Person;

use App\Http\Controllers\Api\v1\Service\Person\PersonService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PersonController extends Controller
{
    protected $personService;
    public function __construct(PersonService $personService)
    {
        $this->personService = $personService;
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
    public function storePerson(Request $request): JsonResponse
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
    public function personOtpValidation(Request $request): JsonResponse
    {
        Log::info('PersonController > personOtpValidation function Inside.' . json_encode($request->all()));
        $response = $this->personService->personOtpValidation($request->all());
        Log::info('PersonController > personOtpValidation function Return.' . json_encode($response));
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
}
