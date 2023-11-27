<?php

namespace App\Http\Controllers\Api\v1\Service\Common;

use App\Http\Controllers\Api\v1\Interface\Common\CommonInterface;
use Illuminate\Support\Facades\Log;

class CommonService
{
    public function __construct(commonInterface $commonInterface)
    {
        $this->commonInterface = $commonInterface;

    }
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }
    public function sendError($errorMessages = [], $error, $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }
    public function getSalutation()
    {
        $result = $this->commonInterface->getSalutation();
        Log::info('CommonService > getSalutation function Return.' . json_encode($result));
        return $this->sendResponse($result, true);
    }
    public function getAllGender()
    {

      $result = $this->commonInterface->getAllGender();
      Log::info('CommonService > getAllGender function Return.' . json_encode($result));
      return $this->sendResponse($result, true);
    }
    public function getAllBloodGroup()
    {

      $result = $this->commonInterface->getAllBloodGroup();
      Log::info('CommonService > getAllBloodGroup function Return.' . json_encode($result));
      return $this->sendResponse($result, true);
    }
    public function getCityByStateId($data)
    {
      Log::info('CommonService > getCityByStateId function Inside.' . json_encode($data));
      $result = $this->commonInterface->getCityByStateId($data['stateId']);
      Log::info('CommonService > getCityByStateId function Return.' . json_encode($result));
      return $this->sendResponse($result, true);
    }

    public function getAllStates()
    {
      $result = $this->commonInterface->getAllStates();
      Log::info('CommonService > getAllStates function Return.' . json_encode($result));
      return $this->sendResponse($result, true);
    }
    public function getAddrerssType()
    {

      $result = $this->commonInterface->getAddrerssType();
      Log::info('CommonService > getAddrerssType function Return.' . json_encode($result));
      return $this->sendResponse($result, true);
    }
    public function getMaritalStatus()
    {
      $result = $this->commonInterface->getMaritalStatus();
      Log::info('CommonService > getMaritalStatus function Return.' . json_encode($result));
      return $this->sendResponse($result, true);
    }
    public function getLanguage()
    {
     $result = $this->commonInterface->getLanguage();
      Log::info('CommonService > getLanguage function Return.' . json_encode($result));
      return $this->sendResponse($result, true);
    }

    public function getAllDocumentType()
    {
      $result = $this->commonInterface->getAllDocumentType();
      Log::info('CommonService > getAllDocumentType function Return.' . json_encode($result));
      return $this->sendResponse($result, true);

    }
    public function getAllBankAccountType()
    {
      $result = $this->commonInterface->getAllBankAccountType();
      Log::info('CommonService > getAllBankAccountType function Return.' . json_encode($result));
      return $this->sendResponse($result, true);
    }


    public function getPersonMasterData()
    {

      $saluationLists = $this->getSalutation();
      $bloodGroupLists = $this->getAllBloodGroup();
      $genderLists = $this->getAllGender();
      $maritalStatusLists = $this->getMaritalStatus();
      $addressOfLists = $this->getAddrerssType();
      $languageLists = $this->getLanguage();
      $idDocumentTypes = $this->getAllDocumentType();
      $bankAccountTypes = $this->getAllBankAccountType();
      $datas = [
        'saluationLists' => $saluationLists,
        'bloodGroupLists' => $bloodGroupLists,
        'genderLists' => $genderLists,
        'maritalStatusLists' => $maritalStatusLists,
        'addressOfLists' => $addressOfLists,
        'languageLists' => $languageLists,
        'idDocumentTypes' => $idDocumentTypes,
        'bankAccountTypes' => $bankAccountTypes
      ];


      return $datas;
    }

}
