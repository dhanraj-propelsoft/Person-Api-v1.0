<?php

namespace App\Http\Controllers\Api\v1\Service\Person;

use App\Http\Controllers\Api\v1\Interface\Common\CommonInterface;
use App\Http\Controllers\Api\v1\Interface\Common\SmsInterface;
use App\Http\Controllers\Api\v1\Interface\Member\MemberInterface;
use App\Http\Controllers\Api\v1\Interface\Person\PersonInterface;
use App\Http\Controllers\Api\v1\Service\Common\CommonService;
use App\Http\Controllers\Api\v1\Service\Common\SmsService;
use App\Models\IdDocumentType;
use App\Models\Person;
use App\Models\PersonAddress;
use App\Models\personAnniversary;
use App\Models\PersonDetails;
use App\Models\PersonEducation;
use App\Models\PersonEmail;
use App\Models\PersonLanguage;
use App\Models\PersonMobile;
use App\Models\PersonProfession;
use App\Models\PersonProfilePic;
use App\Models\PropertyAddress;
use App\Models\TempPerson;
use App\Models\WebLink;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PersonService
{
    protected $personInterface, $commonService;
    public function __construct(PersonInterface $personInterface, CommonService $commonService, MemberInterface $memberInterface, SmsService $smsService, SmsInterface $smsInterface, CommonInterface $CommonInterface)
    {
        $this->personInterface = $personInterface;
        $this->commonService = $commonService;
        $this->memberInterface = $memberInterface;
        $this->smsService = $smsService;
        $this->smsInterface = $smsInterface;
        $this->CommonInterface = $CommonInterface;
    }
    public function findMemberByUid($uid)
    {

        $response = Http::get('http://localhost:8000/api/findMemberByUid/' . $uid);
        $checkPerson = null;
        if ($response->successful()) {
            $responseData = $response->json();

            $checkPerson = $responseData['data'];
        }
        return $checkPerson;
    }
    public function findCredential($datas)
    {
        Log::info('PersonService > findCredential function Inside.' . json_encode($datas));
        $datas = (object) $datas;

        $checkPersonMobile = $this->personInterface->checkPersonByMobileNo($datas->mobileNumber);
        $checkPersonEmail = $this->personInterface->checkPersonByEmail($datas->email);
        if ($checkPersonMobile && !$checkPersonEmail) {
            $personMobileUid = $checkPersonMobile->uid;
            $checkPersonMobileAsMember = $this->findMemberByUid($personMobileUid);
            if ($checkPersonMobileAsMember) {
                dd("case11");
            } else {
                dd("case2");
            }
        } elseif (!$checkPersonMobile && $checkPersonEmail) {
            $personEmailUid = $checkPersonEmail->uid;
            $checkPersonEmailAsMember = $this->findMemberByUid($personEmailUid);
            if ($checkPersonEmailAsMember) {
                dd("case10");
            } else {
                dd("case3");
            }
        } elseif ($checkPersonMobile && $checkPersonEmail) {
            $personMobileUid = $checkPersonMobile->uid;
            $personEmailUid = $checkPersonEmail->uid;

            $checkPersonMobileAsMember = $this->findMemberByUid($personMobileUid);
            $checkPersonEmailAsMember = $this->findMemberByUid($personEmailUid);

            if ($personMobileUid == $personEmailUid) {

                if ($checkPersonMobileAsMember) {
                    dd("case9");
                } else {
                    dd("case5");
                }
            } else {

                if ($checkPersonMobileAsMember && $checkPersonEmailAsMember) {
                    dd("case8");
                } elseif ($checkPersonMobileAsMember && !$checkPersonEmailAsMember) {
                    dd("case6");
                } elseif (!$checkPersonMobileAsMember && $checkPersonEmailAsMember) {
                    dd("case7");
                } else {
                    dd("case4");
                }
            }
        } else {
            dd("case1");
        }
        dd($checkPersonMobile, $checkPersonEmail);
        if (!empty($checkPersonMobile)) {
            $checkPersonEmail = $this->personInterface->checkPersonEmailByUid($datas->email, $checkPersonMobile->uid);
        }
        $personMobile = $this->personInterface->getPersonDataByMobileNo($datas->mobileNumber);

        $personEmail = $this->personInterface->getPersonDataByEmail($datas->email);

        if ($checkPersonMobile && $checkPersonEmail) {
            $result = ['type' => 1, 'personData' => $datas, 'uid' => $checkPersonMobile->uid, 'status' => 'ExactPerson'];
        } else if ($personMobile !== null || $personEmail !== null) {
            $personData = ['personMobile' => $personMobile->mobile, 'personEmail' => $personEmail->email];
            $result = ['type' => 2, 'personData' => $personData, 'status' => 'mappedPerson'];
        } else {
            $result = ['type' => 3, 'status' => 'freshMember'];
        }
        return $this->commonService->sendResponse($result, true);
    }
    public function findMobileNumber($datas)
    {

        $datas = (object) $datas;
        $model = $this->memberInterface->findMemberByMobileNo($datas->mobileNumber);

        if ($model) {
            $memberName = $model->personDetails->first_name;
            $memberUid = $model->personDetails->uid;
            $memberSatge = $model->pfm_stage_id;

            $result = [
                'type' => 1,
                'stage' => $memberSatge,
                'memberName' => $memberName,
                'memberUid' => $memberUid,
                'mobileNumber' => $datas->mobileNumber,
                'status' => "MemberOnly"
            ];
        } else {
            $result = [
                'type' => 2,
                'mobileNumber' => $datas->mobileNumber,
                'status' => "checkingPerson"
            ];
        }
        return $this->commonService->sendResponse($result, "");
    }
    public function storePerson($datas, $type = null)
    {

        $datas['personUid'] = isset($datas['personUid']) ? $datas['personUid'] : null;

        $datas = (object) $datas;
        $personModel = $this->convertToPersonModel($datas);
        $personDetailModel = $this->convertToPersonDetailModel($datas);
        $personEmailModel = $this->convertToPersonEmailModel($datas);
        $personMobileModel = $this->convertToPersonMobileModel($datas);
        $personProfileModel = $this->convertToPersonProfileModel($datas);
        $personAnniversaryDate = $this->convertToPersonAnniversaryDate($datas);

        $personAnotherEmailModel = array();

        if (isset($datas->secondEmail) && !in_array(null, $datas->secondEmail)) {
            $personAnotherEmailModel = $this->convertToPersonEmailModelAnother($datas);
        }
        $personAnotherMobileModel = array();
        if (isset($datas->secondNumber) && !in_array(null, $datas->secondNumber)) {
            $personAnotherMobileModel = $this->convertToPersonMobileModelAnother($datas);
        }
        $personWebLink = array();
        if (isset($datas->webLinks) && !in_array(null, $datas->webLinks)) {
            $personWebLink = $this->convertToPersonWebLink($datas);
        }
        $personOtherLanguage = array();
        if ((isset($datas->otherLanguage) && $datas->otherLanguage !== null) || isset($datas->motherLanguage) && !in_array(null, $datas->motherLanguage)) {
            $personOtherLanguage = $this->convertToPersonOtherLanguage($datas);
        }

        $personIdDocument = array();
        if ((isset($datas->idDocumentType) && $datas->idDocumentType !== null)) {
            $personIdDocument = $this->convertToPersonIdDocument($datas);
        }
        $personEducationModel = array();
        if (isset($datas->Qualification) && !in_array(null, $datas->Qualification)) {
            $personEducationModel = $this->convertToPersonEducation($datas);
        }
        $personProfessionModel = array();
        if (isset($datas->ProfessionDepartment) && !in_array(null, $datas->ProfessionDepartment)) {
            $personProfessionModel = $this->convertToPersonProfession($datas);
        }

        $personCommonAddressModel = array();
        $personAddressId = array();
        if ((isset($datas->addressOf) && $datas->addressOf !== null)) {
            $addressId = isset($datas->propertyAddressId) ? $datas->propertyAddressId : null;
            Log::info('PersonService > addressId function Inside.' . json_encode($addressId));
            if ($addressId) {
                for ($i = 0; $i < count($datas->propertyAddressId); $i++) {
                    $perviousAddress = $this->personInterface->checkPerivousAddressById($datas->propertyAddressId[$i], $datas->personUid);
                }
            }
            $personCommonAddressModel = $this->convertToPersonCommonAddress($datas);
            $personAddressId = $this->convertToPersonAddressId($datas);
            Log::info('PersonService > personAddressId function Return.' . json_encode($personAddressId));
        }
        $allModels = [
            'personModel' => $personModel,
            'personDetailModel' => $personDetailModel,
            'personEmailModel' => $personEmailModel,
            'personMobileModel' => $personMobileModel,
            'personAnotherEmailModel' => $personAnotherEmailModel,
            'personAnotherMobileModel' => $personAnotherMobileModel,
            'personWebLink' => $personWebLink,
            'personOtherLanguage' => $personOtherLanguage,
            'personIdDocument' => $personIdDocument,
            'personEducationModel' => $personEducationModel,
            'personProfessionModel' => $personProfessionModel,
            'personCommonAddressModel' => $personCommonAddressModel,
            'personAddressId' => $personAddressId,
            'personAnniversaryDate' => $personAnniversaryDate,
            'personProfileModel' => $personProfileModel,

        ];
        $personData = $this->personInterface->storePerson($allModels);
        log::info('allModels' . json_encode($personData));

        Log::info('PersonService > storePerson function Return.' . json_encode($personData));
        if (isset($datas->type) && $datas->type == "resource") {
            return $personData;
        } else {
            if ($personData['message'] == "Success") {
                if (!$datas->personUid) {
                    $uid = $personData['data']->uid->toString();
                    $createTableBasedUid = $this->createPersonTableByUid($uid);
                }
                return $this->commonService->sendResponse($personData['data'], $personData['message']);
            } else {
                return $this->commonService->sendError($personData['data'], $personData['message']);
            }
        }
    }

    public function convertToPersonModel($datas)
    {
        Log::info('PersonService > uidByPerson.' . json_encode($datas->personUid));
        if ($datas->personUid) {
            $model = $this->personInterface->getPersonByUid($datas->personUid);
        } else {
            $model = new Person();
            $model->uid = Str::uuid();
        }
        $model->pfm_stage_id = isset($datas->stageId) ? $datas->stageId : 1;
        $model->pfm_origin_id = isset($datas->originId) ? $datas->originId : 1;
        $model->pfm_existence_id = isset($datas->existingId) ? $datas->existingId : 1;
        $model->reason = isset($datas->reason) ? $datas->reason : null;
        $model->pfm_active_status_id = isset($datas->activeStatusId) ? $datas->activeStatusId : 1;
        Log::info('PersonService > personUid .' . json_encode($model));
        return $model;
    }

    public function convertToPersonDetailModel($datas)
    {
        Log::info('PersonService > convertToPersonDetailModel function Inside.' . json_encode($datas));
        if ($datas->personUid) {
            $model = $this->personInterface->getPersonDatasByUid($datas->personUid);
        } else {
            $model = new PersonDetails();
        }

        $model->pims_person_salutation_id = isset($datas->salutationId) ? $datas->salutationId : null;
        $model->first_name = $datas->firstName;
        $model->middle_name = isset($datas->middleName) ? $datas->middleName : null;
        $model->last_name = isset($datas->lastName) ? $datas->lastName : null;
        $model->nick_name = isset($datas->nickName) ? $datas->nickName : null;
        $date = null;
        if (isset($datas->dob)) {
            $date = Carbon::createFromFormat('d-m-Y', $datas->dob)->format('Y-m-d');
        }
        $model->dob = $date;
        $model->birth_place = isset($datas->birthCity) ? $datas->birthCity : null;
        $model->pims_person_marital_status_id = isset($datas->maritalStatus) ? $datas->maritalStatus : null;
        $model->pims_person_gender_id = isset($datas->genderId) ? $datas->genderId : null;
        $model->pims_person_blood_group_id = isset($datas->bloodGroup) ? $datas->bloodGroup : null;
        $model->pfm_survial_id = isset($datas->survialId) ? $datas->survialId : 1;
        $model->pims_person_country_id = isset($datas->countryId) ? $datas->countryId : null;
        $model->decesaed_date = isset($datas->decesaedDate) ? $datas->decesaedDate : null;
        $model->comments = isset($datas->comment) ? $datas->comment : null;
        $model->pfm_active_status_id = isset($datas->activeStatusId) ? $datas->activeStatusId : 1;
        Log::info('PersonService > convertToPersonDetailModel function Return.' . json_encode($model));

        return $model;
    }
    public function convertToPersonEmailModel($datas)
    {
        Log::info('PersonService > convertToPersonEmailModel function Inside.' . json_encode($datas));
        if ($datas->personUid) {
            $model = $this->personInterface->getPersonEmailByUid($datas->personUid);
        } else {
            $model = new PersonEmail();
        }
        $model->email = $datas->email;
        $model->email_cachet_id = isset($datas->cachetId) ? $datas->cachetId : 1;
        $model->pfm_active_status_id = isset($datas->activeStatusId) ? $datas->activeStatusId : 1;
        Log::info('PersonService > convertToPersonEmailModel function Return.' . json_encode($model));
        return $model;
    }

    public function convertToPersonMobileModel($datas)
    {
        Log::info('PersonService > convertToPersonMobileModel function Inside.' . json_encode($datas));
        if ($datas->personUid) {
            $model = $this->personInterface->getPersonMobileNoByUid($datas->personUid, $datas->mobileNumber);
        } else {
            $model = new PersonMobile();
        }
        $model->mobile_no = $datas->mobileNumber;
        $model->country_id = isset($datas->countryId) ? $datas->countryId : null;
        $model->mobile_cachet_id = isset($datas->cachetId) ? $datas->cachetId : 1;
        $model->pfm_active_status_id = isset($datas->activeStatusId) ? $datas->activeStatusId : 1;
        Log::info('PersonService > convertToPersonMobileModel function Return.' . json_encode($model));
        return $model;
    }
    public function convertToPersonProfileModel($datas)
    {
        if (isset($datas->personProfile)) {
            $personPic = $this->personInterface->getPersonProfileByUid($datas->personUid);
            if ($personPic) {
                $filePathToDelete = storage_path('app/public/Profiles/' . $personPic->profile_pic);
                if (File::exists($filePathToDelete)) {
                    File::delete($filePathToDelete);
                    $personPic->delete();
                }
            }
            $decodedImageContents = base64_decode($datas->personProfile);
            $uniqueFilename = date('YmdHis') . '_' . uniqid() . '.jpg';
            $savePath = storage_path('app/public/Profiles/' . $uniqueFilename);
            Log::info('PersonService >  savePath function Return.' . json_encode($savePath));
            File::put($savePath, $decodedImageContents);
            $model = new PersonProfilePic();
            $model->uid = $datas->personUid;
            $model->profile_pic = $uniqueFilename;
            $model->profile_cachet_id = isset($datas->cachetId) ? $datas->cachetId : null;
            $model->pfm_active_status_id = isset($datas->activeStatusId) ? $datas->activeStatusId : 1;
            return $model;
        }
    }
    public function convertToPersonAnniversaryDate($datas)
    {
        Log::info('PersonService > PersonAnniversaryDate.' . json_encode($datas->personUid));

        if (isset($datas->anniversaryDate)) {
            if ($datas->personUid) {
                $model = $this->personInterface->personGetAnniversaryDate($datas->personUid);
            } else {
                $model = new personAnniversary();
                $model->uid = $datas->personUid;
            }
            $date = Carbon::createFromFormat('d-m-Y', $datas->anniversaryDate)->format('Y-m-d');
            $model->anniversary_date = $date;
            $model->occasions_id = isset($datas->occasionId) ? $datas->occasionId : null;
            $model->pfm_active_status_id = isset($datas->activeStatusId) ? $datas->activeStatusId : 1;
            Log::info('PersonService > PersonAnniversaryDate .' . json_encode($model));
            return $model;
        }
    }
    public function convertToPersonEmailModelAnother($datas)
    {
        $orgModel = [];
        Log::info('PersonService > convertToPersonEmailModelAnother function Inside.' . json_encode($datas));
        for ($i = 0; $i < count($datas->secondEmail); $i++) {
            $checkEmail = $this->personInterface->checkSecondaryEmailByUid($datas->secondEmail[$i], $datas->personUid);
            if ($checkEmail) {
                $checkEmail->uid = $datas->personUid;
                $checkEmail->email = $datas->secondEmail[$i];
                $checkEmail->email_cachet_id = 2;
                $checkEmail->save();
            } else {
                $model[$i] = new PersonEmail();
                $model[$i]->email = $datas->secondEmail[$i];
                $model[$i]->email_cachet_id = 2;
                array_push($orgModel, $model[$i]);
            }
        }
        Log::info('PersonService > convertToPersonEmailModelAnother3 function Return.' . json_encode($orgModel));
        return $orgModel;
    }
    public function convertToPersonMobileModelAnother($datas)
    {
        $orgModel = [];
        Log::info('PersonService > convertToPersonMobileModelAnother function Inside.' . json_encode($datas));
        for ($i = 0; $i < count($datas->secondNumber); $i++) {
            $checkMobile = $this->personInterface->checkSecondaryMobileNumberByUid($datas->secondNumber[$i], $datas->personUid);
            if ($checkMobile) {
                $checkMobile->uid = $datas->personUid;
                $checkMobile->mobile_no = $datas->secondNumber[$i];
                $checkMobile->country_id = isset($datas->countryId[$i]) ? $datas->countryId[$i] : null;
                $checkMobile->mobile_cachet_id = isset($datas->cachetId[$i]) ? $datas->cachetId[$i] : null;
                $checkMobile->pfm_active_status_id = isset($datas->activeStatusId[$i]) ? $datas->activeStatusId[$i] : 1;
                $checkMobile->save();
            } else {
                $model[$i] = new PersonMobile();
                $model[$i]->mobile_no = $datas->secondNumber[$i];
                $model[$i]->country_id = isset($datas->countryId[$i]) ? $datas->countryId[$i] : null;
                $model[$i]->mobile_cachet_id = isset($datas->cachetId[$i]) ? $datas->cachetId[$i] : 2;
                $model[$i]->pfm_active_status_id = isset($datas->activeStatusId[$i]) ? $datas->activeStatusId[$i] : 1;
                array_push($orgModel, $model[$i]);
            }
        }
        Log::info('PersonService > convertToPersonMobileModelAnother function Return.' . json_encode($orgModel));
        return $orgModel;
    }
    public function convertToPersonWebLink($datas)
    {
        $orgModel = [];
        Log::info('PersonService > convertToPersonWebLink function Inside.' . json_encode($datas));
        $link = $datas->webLinks;
        for ($i = 0; $i < count($link); $i++) {
            if ($link[$i]) {
                $model[$i] = new WebLink();
                $model[$i]->web_add = $link[$i];
                $model[$i]->web_cachet_id = isset($datas->cachetId[$i]) ? $datas->cachetId[$i] : null;
                $model[$i]->pfm_active_status_id = isset($datas->activeStatusId[$i]) ? $datas->activeStatusId[$i] : 1;
                array_push($orgModel, $model[$i]);
            }
        }
        Log::info('PersonService > convertToPersonWebLinkEnd function Return.' . json_encode($orgModel));
        return $orgModel;
    }
    public function convertToPersonOtherLanguage($datas)
    {
        $orgModel = [];
        Log::info('PersonService > convertToPersonOtherLanguage function Inside.' . json_encode($datas));
        if (isset($datas->otherLanguage)) {
            if ($datas->personUid) {
                $models = $this->personInterface->personMotherTongueByUid($datas->personUid);
            }
            if (isset($models) && count($models)) {
                foreach ($models as $model) {
                    $model->delete();
                }
            }
            for ($i = 0; $i < count($datas->otherLanguage); $i++) {
                if ($datas->otherLanguage[$i]) {
                    $result[$i] = new PersonLanguage();
                    $result[$i]->pims_com_language_id = $datas->otherLanguage[$i];
                    $result[$i]->is_mother_tongue = $datas->motherLanguage;
                    $result[$i]->spoken = isset($datas->spoken) ? $datas->spoken : null;
                    $result[$i]->read = isset($datas->read) ? $datas->read : null;
                    $result[$i]->write = isset($datas->write) ? $datas->write : null;
                    $result[$i]->pfm_active_status_id = isset($datas->activeStatusId) ? $datas->activeStatusId : 1;
                    array_push($orgModel, $result[$i]);
                }
            }
        }
        if ($datas->motherLanguage && empty($orgModel)) {
            $language = $datas->motherLanguage;
            if ($datas->personUid) {
                $models = $this->personInterface->personMotherTongueByUid($datas->personUid);
            }
            if (isset($models) && count($models)) {
                foreach ($models as $model) {
                    $model->delete();
                }
            }
            for ($i = 0; $i < count($language); $i++) {
                if ($language[$i]) {
                    $result[$i] = new PersonLanguage();
                    $result[$i]->is_mother_tongue = $language[$i];
                    $result[$i]->spoken = isset($datas->spoken) ? $datas->spoken : null;
                    $result[$i]->read = isset($datas->read) ? $datas->read : null;
                    $result[$i]->write = isset($datas->write) ? $datas->write : null;
                    $result[$i]->pfm_active_status_id = isset($datas->activeStatusId) ? $datas->activeStatusId : 1;
                    array_push($orgModel, $result[$i]);
                }
            }
        }
        Log::info('PersonService > convertToPersonOtherLanguage123 function Return.' . json_encode($orgModel));
        return $orgModel;
    }
    public function convertToPersonIdDocument($datas)
    {
        Log::info('PersonService > convertToPersonIdDocument function Inside.' . json_encode($datas));
        $orgModel = [];
        for ($i = 0; $i < count($datas->idDocumentType); $i++) {
            if ($datas->idDocumentType[$i]) {
                $model[$i] = new IdDocumentType();
                $model[$i]->pims_person_doc_type_id = $datas->idDocumentType[$i];
                $model[$i]->Doc_no = $datas->documentNumber[$i];
                $model[$i]->doc_validity = $datas->validTill[$i];
                if (property_exists($datas, 'attachments') && isset($datas->attachments[$i])) {
                    $model[$i]->attachment = $datas->attachments[$i];
                } else {
                    $model[$i]->attachment = null; // Setting a default value, change as needed
                }

                $model[$i]->doc_cachet_id = isset($datas->cachetId[$i]) ? $datas->cachetId[$i] : null;
                $model[$i]->pfm_active_status_id = isset($datas->activeStatus[$i]) ? $datas->activeStatus[$i] : 1;
                array_push($orgModel, $model[$i]);
            }
        }

        Log::info('PersonService > convertToPersonIdDocument function Return.' . json_encode($orgModel));
        return $orgModel;
    }
    public function convertToPersonEducation($datas)
    {
        $orgModel = [];
        Log::info('PersonService > convertToPersonEducation function Inside.' . json_encode($datas));
        for ($i = 0; $i < count($datas->Qualification); $i++) {
            if ($datas->Qualification[$i]) {
                $model[$i] = new PersonEducation();
                $model[$i]->pims_person_qualification_id = $datas->Qualification[$i];
                $model[$i]->year_of_Pass = $datas->passedYear[$i];
                $model[$i]->mark = $datas->mark[$i];
                $model[$i]->pfm_active_status_id = isset($datas->activeStatusId[$i]) ? $datas->activeStatusId[$i] : null;
                array_push($orgModel, $model[$i]);
            }
        }
        Log::info('PersonService > convertToPersonEducation function Return.' . json_encode($orgModel));
        return $orgModel;
    }
    public function convertToPersonProfession($datas)
    {
        $orgModel = [];
        Log::info('PersonService > convertToPersonProfession function Inside.' . json_encode($datas));
        for ($i = 0; $i < count($datas->ProfessionDepartment); $i++) {
            if ($datas->ProfessionDepartment[$i]) {
                $model[$i] = new PersonProfession();
                $model[$i]->department_id = $datas->ProfessionDepartment[$i];
                $model[$i]->designation_id = $datas->Designation[$i];
                $model[$i]->org_id = isset($datas->organization[$i]) ? $datas->organization[$i] : null;
                // $model[$i]->doj=$datas->joinDate[$i];
                //  $model[$i]->dor=$datas->reliveDate[$i];
                $model[$i]->experience = $datas->experinceYear[$i];
                $model[$i]->reason = isset($datas->reason[$i]) ? $datas->reason[$i] : null;
                $model[$i]->pfm_active_status_id = isset($datas->activeStatusId[$i]) ? $datas->activeStatusId[$i] : null;
                array_push($orgModel, $model[$i]);
            }
        }
        Log::info('PersonService > convertToPersonProfession function Return.' . json_encode($orgModel));
        return $orgModel;
    }
    public function convertToPersonCommonAddress($datas)
    {
        $orgModel = [];
        Log::info('PersonService > convertToPersonCommonAddress function Inside.' . json_encode($datas));
        for ($i = 0; $i < count($datas->addressOf); $i++) {
            if ($datas->addressOf[$i]) {
                $model[$i] = new PropertyAddress();
                $model[$i]->pims_com_address_type_id = isset($datas->addressOf[$i]) ? $datas->addressOf[$i] : null;
                $model[$i]->door_no = isset($datas->doorNo[$i]) ? $datas->doorNo[$i] : null;
                $model[$i]->building_name = isset($datas->buildingName[$i]) ? $datas->buildingName[$i] : null;
                $model[$i]->pin = isset($datas->pinCode[$i]) ? $datas->pinCode[$i] : null;
                $model[$i]->area = isset($datas->area[$i]) ? $datas->area[$i] : null;
                $model[$i]->street = isset($datas->street[$i]) ? $datas->street[$i] : null;
                $model[$i]->land_mark = isset($datas->landMark[$i]) ? $datas->landMark[$i] : null;
                $model[$i]->pims_com_district_id = isset($datas->district[$i]) ? $datas->district[$i] : null;
                $model[$i]->pims_com_city_id = isset($datas->city[$i]) ? $datas->city[$i] : null;
                $model[$i]->pims_com_state_id = isset($datas->state[$i]) ? $datas->state[$i] : null;
                $model[$i]->pims_com_country_id = isset($datas->country[$i]) ? $datas->country[$i] : null;
                $model[$i]->location = isset($datas->location[$i]) ? $datas->location[$i] : null;
                $model[$i]->google_link = isset($datas->googleLink[$i]) ? $datas->googleLink[$i] : null;
                $model[$i]->latitude = isset($datas->latitude[$i]) ? $datas->latitude[$i] : null;
                $model[$i]->longitude = isset($datas->longitude[$i]) ? $datas->longitude[$i] : null;
                $model[$i]->pfm_active_status_id = isset($datas->longitude[$i]) ? $datas->longitude[$i] : null;

                array_push($orgModel, $model[$i]);
            }
        }
        Log::info('PersonService > convertToPersonCommonAddress function Return.' . json_encode($orgModel));

        return $orgModel;
    }
    public function convertToPersonAddressId($datas)
    {
        $orgModel = [];
        for ($i = 0; $i < count($datas->addressOf); $i++) {
            $model[$i] = new PersonAddress();
            // $model[$i]->uid = $datas->personUid;
            $model[$i]->address_cachet_id = 1;
            array_push($orgModel, $model[$i]);
        }
        Log::info('PersonService > convertToPersonAddressId function Inside.' . json_encode($orgModel));
        return $orgModel;
    }
    public function createPersonTableByUid($uid)
    {
        if ($uid) {
            Schema::create($uid, function ($table) {
                $table->id();
                $table->string('org_id');
                $table->integer('pfm_active_status_id')->nullable();
                $table->integer('deleted_flag')->nullable();
                $table->timestamps();
                $table->timestamp('deleted_at')->nullable();
            });
        }
    }
    public function storeTempPerson($datas)
    {

        Log::info('PersonService > storeTempPerson function Inside.' . json_encode($datas));
        $datas = (object) $datas;
        $tempId = isset($datas->tempId) ? $datas->tempId : null;
        $model = $this->convertToTempPersonModel($datas, $tempId);
        $storeTempPerson = $this->personInterface->storeTempPerson($model);
        Log::info('PersonService > storeTempPerson function Return.' . json_encode($storeTempPerson));

        if ($storeTempPerson['message'] == "Success") {

            $responseModel = $storeTempPerson['data'];
            if ($responseModel->pfm_stage_id == 1) {
                $salutationModel = $this->commonService->getSalutation();
                $responseData = ['tempModel' => $storeTempPerson['data'], 'salutationModel' => $salutationModel];
            } else if ($responseModel->pfm_stage_id == 2) {
                $gender = $this->commonService->getAllGender();
                $bloodGroup = $this->commonService->getAllBloodGroup();
                $responseData = ['tempModel' => $responseModel, 'gender' => $gender, 'bloodGroup' => $bloodGroup];
            } elseif ($responseModel->pfm_stage_id == 3) {
                $temp = ['tempId' => $tempId];
                $storeTempPerson1 = $this->resendOtp($temp);
                log::info('personservice > ' . json_encode($storeTempPerson1));
                return $storeTempPerson1;
            }
            return $this->commonService->sendResponse($responseData, $storeTempPerson['message']);
        } else {
            return $this->commonService->sendError($storeTempPerson['data'], $storeTempPerson['message']);
        }
    }
    public function convertToTempPersonModel($datas, $id = null)
    {
        log::info('personService > convertToTempPersonModel ' . json_encode($datas));

        if ($id) {
            $model = TempPerson::findOrFail($id);
            log::info('findOrFail > ' . json_encode($model));
        } else {

            $model = new TempPerson();
        }
        if (isset($datas->mobileNumber)) {
            $model->mobile_no = isset($datas->mobileNumber) ? $datas->mobileNumber : "";
        }
        if (isset($datas->email)) {
            $model->email = isset($datas->email) ? $datas->email : "";
        }

        $salutation = isset($datas->salutation) ? $datas->salutation : "";
        if ($salutation) {
            $model['personal_data->salutation'] = $salutation;
        }

        $firstName = isset($datas->firstName) ? $datas->firstName : "";
        $middleName = isset($datas->middleName) ? $datas->middleName : "";
        $lastName = isset($datas->lastName) ? $datas->lastName : "";
        $nickName = isset($datas->nickName) ? $datas->nickName : "";
        $gender = isset($datas->gender) ? $datas->gender : "";
        $dob = isset($datas->dob) ? $datas->dob : "";
        $bloodGroup = isset($datas->bloodGroup) ? $datas->bloodGroup : "";
        $otp = isset($datas->otp) ? $datas->otp : "";
        $stage = isset($datas->stage) ? $datas->stage : "";
        if ($stage) {
            log::info('personService > stage' . json_encode($datas->stage));
            $model->pfm_stage_id = $stage;
        }

        if ($firstName) {
            $model['personal_data->firstName'] = $firstName;
        }
        if ($middleName) {
            $model['personal_data->middleName'] = $middleName;
        }
        if ($lastName) {
            $model['personal_data->lastName'] = $lastName;
        }
        if ($nickName) {
            $model['personal_data->nickName'] = $nickName;
        }
        if ($gender) {
            $model['personal_data->gender'] = $gender;
        }
        if ($bloodGroup) {
            $model['personal_data->bg'] = $bloodGroup;
        }
        if ($dob) {
            $model['personal_data->dob'] = $dob;
        }
        if ($otp) {
            log::info('personService > otp' . json_encode($datas->otp));
            $model->otp = $otp;
        }

        return $model;
    }
    public function resendOtp($datas)
    {
        Log::info('PersonService > resendOtp function Inside.' . json_encode($datas));
        $datas = (object) $datas;
        $tempId = $datas->tempId;
        $otp = random_int(1000, 9999);
        $newDatas = ['otp' => $otp, 'stage' => 4];
        $newDatas = (object) $newDatas;
        $model = $this->convertToTempPersonModel($newDatas, $tempId);
        $storeTempPerson = $this->personInterface->storeTempPerson($model);
        Log::info('PersonService > findMobileNumber function Return.' . json_encode($storeTempPerson));
        if ($storeTempPerson['message'] == "Success") {
            return $this->commonService->sendResponse($storeTempPerson['data'], $storeTempPerson['message']);
        } else {
            return $this->commonService->sendError($storeTempPerson['data'], $storeTempPerson['message']);
        }
    }
    public function personOtpValidation($datas)
    {

        Log::info('PersonService > personOtpValidation function Inside.' . json_encode($datas));
        $datas = (object) $datas;
        $tempPersonModel = $this->personInterface->findTempPersonById($datas->tempId);
        Log::info('PersonService > personOtpValidation function Return.' . json_encode($tempPersonModel));
        if ($tempPersonModel) {
            if ($datas->otp == $tempPersonModel->otp) {
                $personalDatas = json_decode($tempPersonModel->personal_data, true);
                $mobileNumber = isset($tempPersonModel['mobile_no']) ? $tempPersonModel['mobile_no'] : null;
                $email = isset($tempPersonModel['email']) ? $tempPersonModel['email'] : null;
                $salutation = isset($personalDatas['salutation']) ? $personalDatas['salutation'] : null;
                $firstName = isset($personalDatas['firstName']) ? $personalDatas['firstName'] : null;
                $middleName = isset($personalDatas['middleName']) ? $personalDatas['middleName'] : null;
                $lastName = isset($personalDatas['lastName']) ? $personalDatas['lastName'] : null;
                $nickName = isset($personalDatas['nickName']) ? $personalDatas['nickName'] : null;
                $gender = isset($personalDatas['gender']) ? $personalDatas['gender'] : null;
                $bloodGroup = isset($personalDatas['bg']) ? $personalDatas['bg'] : null;
                $dob = isset($personalDatas['dob']) ? $personalDatas['dob'] : null;
                $personDatas = ['mobileNumber' => $mobileNumber, 'email' => $email, 'salutationId' => $salutation, 'firstName' => $firstName, 'middleName' => $middleName, 'lastName' => $lastName, 'nickName' => $nickName, 'genderId' => $gender, 'bloodGroup' => $bloodGroup, 'dob' => $dob];

                $personModel = $this->storePerson($personDatas);
                $tempPersonModel->delete();
                return $personModel;
            } else {
                return $this->commonService->sendError(['tempId' => $tempPersonModel->id, 'mobileNumber' => $tempPersonModel->mobile_no]);
            }
        } else {
            return $this->commonService->sendError(['InValid Person', false]);
        }
    }
    public function generateEmailOtp($datas)
    {
        Log::info('PersonService > generateEmailOtp function Inside.' . json_encode($datas));
        $datas = (object) $datas;
        $personData = $this->personInterface->getPersonEmailByUid($datas->uid);
        $otp = substr(str_shuffle("123456789"), 0, 5);
        $setOtpEmail = $this->personInterface->setOtpForPersonPrimaryEmail($datas->uid, $personData->email, $otp);
        if ($setOtpEmail) {
            $getMemberName = $this->personInterface->getPersonDatasByUid($datas->uid);
            $response = ['type' => '1', 'uid' => $datas->uid, 'email' => $personData->email, 'personName' => $getMemberName['first_name']];
            return $this->commonService->sendResponse($response, true);
        } else {
            $response = ["message" => 'Mail Not Send', 'type' => '2'];
            return $this->commonService->sendError($response, false);
        }
    }
    public function checkMemberOrPerson($datas)
    {

        Log::info('PersonService > checkMemberOrPerson function Inside.' . json_encode($datas));
        $datas = (object) $datas;
        $checkMember = $this->memberInterface->findMemberDataByUid($datas->uid);

        if ($checkMember) {
            $personName = $this->personInterface->getPersonDatasByUid($datas->uid);
            return $this->commonService->sendResponse($personName, 'ExactMember');
        } else {
            $mobileOtp = $this->personMobileOtp($datas);
            return $mobileOtp;
        }
    }
    public function personMobileOtp($datas)
    {

        Log::info('PersonService > personMobileOtp function Inside.' . json_encode($datas));
        $datas = (object) $datas;
        $otp = random_int(1000, 9999);
        $setOtpMobile = $this->personInterface->setOtpMobileNo($datas->uid, $datas->mobileNumber, $otp);
        if ($setOtpMobile) {
            $smsTypeModel = $this->smsInterface->findSmsTypeByName('PersonToMember');
            $smsHistoryModel = $this->smsService->storeSms($datas->mobileNumber, $smsTypeModel->id, $otp, $datas->uid);
            Log::info('PersonService > personMobileOtp function Return.' . json_encode($datas));
            return $this->commonService->sendResponse($datas, 'OtpSuccesfully');
        } else {
            return $this->commonService->sendError('MobileNo Not Found', false);
        }
    }
    public function checkPersonEmail($datas)
    {

        Log::info('PersonService > checkPersonEmail function Inside.' . json_encode($datas));
        $datas = (object) $datas;
        $checkEmailByUid = $this->personInterface->checkPersonEmailByUid($datas->email, $datas->personUid);
        $findEmailByPereson = $this->personInterface->findEmailByPersonEmail($datas->email);
        Log::info('PersonService > checkPersonEmail function Return.' . json_encode($checkEmailByUid));
        if ($checkEmailByUid) {
            $result = ['type' => 1, 'personDatas' => $checkEmailByUid, 'mobileNumber' => $datas->mobileNumber, 'status' => "Email In User"];
        } elseif ($findEmailByPereson) {
            $result = ['type' => 2, 'personDatas' => $findEmailByPereson, 'status' => "mutiplePerson"];
        } else {
            $result = ['type' => 3, 'personDatas' => null, 'status' => "Email Not Found"];
        }
        return $this->commonService->sendResponse($result, true);
    }
    public function personDatas($datas)
    {

        Log::info('PersonService > personDatas function Inside.' . json_encode($datas));
        $datas = (object) $datas;
        $model = $this->personInterface->getPersonDatasByUid($datas->uid);
        Log::info('PersonService > personDatas function Return.' . json_encode($model));
        $salutation = $this->CommonInterface->getSalutation();
        $result = ['personData' => $model, 'salutation' => $salutation];
        return $this->commonService->sendResponse($result, true);
    }
    public function personUpdate($datas)
    {

        Log::info('PersonService > personUpdate function Inside.' . json_encode($datas));
        $datas = (object) $datas;
        $personData = $this->personInterface->getPersonDatasByUid($datas->uid);
        $personUpdate = $this->updatePerson($personData, $datas);
        $saveperson = $this->personInterface->savePersonDatas($personUpdate);
        $gender = $this->CommonInterface->getAllGender();
        $bloodGroup = $this->CommonInterface->getAllBloodGroup();
        $result = ['gender' => $gender, 'bloodGroup' => $bloodGroup, 'personData' => $personData];
        return $this->commonService->sendResponse($result, true);
    }
    public function updatePerson($personData, $datas)
    {
        Log::info('PersonService > updatePerson function Inside.' . json_encode($datas));
        Log::info('PersonService > updatePerson function Inside.' . json_encode($personData));
        if ($datas->uid) {
            $personData->uid = isset($datas->uid) ? $datas->uid : null;
            $personData->pims_person_salutation_id = isset($datas->salutation) ? $datas->salutation : null;
            $personData->first_name = isset($datas->firstName) ? $datas->firstName : null;
            $personData->middle_name = isset($datas->middleName) ? $datas->middleName : null;
            $personData->last_name = isset($datas->lastName) ? $datas->lastName : null;
            $personData->nick_name = isset($datas->nickName) ? $datas->nickName : null;
            Log::info('PersonService > updatePerson function Return.' . json_encode($personData));
            return $personData;
        }
    }
    public function personToMember($datas)
    {

        Log::info('PersonService > personToMember function Inside.' . json_encode($datas));
        $datas = (object) $datas;
        $person = $this->personInterface->getPersonDatasByUid($datas->uid);
        $convertPerson = $this->convertPerson($person, $datas);
        $savePerson = $this->personInterface->savePersonDatas($convertPerson);
        return $this->commonService->sendResponse($person, true);
    }
    public function convertPerson($person, $datas)
    {
        Log::info('PersonService > convertPerson function Inside.' . json_encode($datas));
        Log::info('PersonService > convertPerson function Inside.' . json_encode($person));
        $person->uid = isset($datas->uid) ? $datas->uid : null;
        $person->dob = isset($datas->dob) ? $datas->dob : null;
        $person->pims_person_gender_id = isset($datas->gender) ? $datas->gender : null;
        $person->pims_person_blood_group_id = isset($datas->bloodGroup) ? $datas->bloodGroup : null;
        Log::info('PersonService > convertPerson function Return.' . json_encode($person));
        return $person;
    }
    public function emailOtpValidation($datas)
    {
        Log::info('PersonService > emailOtpValidation function Inside.' . json_encode($datas));
        $datas = (object) $datas;
        $uid = $datas->uid;
        $model = $this->personInterface->checkPersonEmailByUid($datas->email, $uid);
        Log::info('PersonService > emailOtpValidation function Return.' . json_encode($model));
        if ($model->otp_received == $datas->otp) {
            $emailStatusUpdate = $this->personInterface->personEmailStatusUpdate($uid, $datas->email);
            $setSatge = $this->personInterface->setStageInMember($uid);
            $result = ['status' => 'Otp Verified', 'type' => 1, 'uid' => $uid];
        } else {
            $result = ['status' => 'Otp Verified Failed', 'type' => 2, 'uid' => $uid];
        }
        return $this->commonService->sendResponse($result, true);
    }
    public function personProfileDetails($datas)
    {

        Log::info('PersonService > personProfileDetails function Inside.' . json_encode($datas));
        $datas = (object) $datas;
        $uid = $datas->uid;
        $personDetails = $this->personInterface->getPersonPrimaryDataByUid($uid);
        $personAddress = $this->personInterface->personAddressByUid($uid);
        $personMasterData = $this->commonService->getPersonMasterData();
        $secondaryMobile = $this->personInterface->personSecondaryMobileByUid($uid);
        $secondaryEmail = $this->personInterface->personSecondaryEmailByUid($uid);

        Log::info('PersonService > personProfileDetails function Return.' . json_encode($personMasterData));
        $datas = [
            'personDetail' => $personDetails,
            'personAddressByUid' => $personAddress,
            'personMasterData' => $personMasterData,
            'secondaryMobile' => $secondaryMobile,
            'secondaryEmail' => $secondaryEmail,
        ];
        return $this->commonService->sendResponse($datas, true);
    }
    public function getPersonAllDetails($datas)
    {

        Log::info('PersonService > getPersonAllDetails function Inside.' . json_encode($datas));
        $datas = (object) $datas;
        $personMobile = $this->personInterface->getPersonDataByMobileNo($datas->mobileNo);

        $personEmail = $this->personInterface->getPersonDataByEmail($datas->email);

        $personData = ['personMobile' => $personMobile, 'personEmail' => $personEmail];
        return $this->commonService->sendResponse($personData, true);
    }
    public function memberAllDetails($datas)
    {
        Log::info('PersonService > memberAllDetails function Inside.' . json_encode($datas));
        $datas = (object) $datas;
        $member = $this->personInterface->getAllDatasInMember($datas->uid);
        $personDetails = $member['personDetails'];
        $primaryMobile = $member['mobile'];
        $primaryEmail = $member['email'];
        $profilePic = $member['profilePic'];
        $personGender = $member['personDetails']['gender'];
        $personbloodGroup = $member['personDetails']['bloodGroup'];
        $primaryAddress = isset($member['personAddress']['ParentComAddress']) ? $member['personAddress']['ParentComAddress'] : null;
        $personEducation = $member['personEducation'];
        $personProfession = $member['personProfession'];

        $data = ['memberDeatils' => $personDetails, 'primaryMobile' => $primaryMobile, 'primaryEmail' => $primaryEmail, 'profilePic' => $profilePic, 'memberGender' => $personGender, 'memberBloodGroup' => $personbloodGroup, 'primaryAddress' => $primaryAddress, 'memberEducation' => $personEducation, 'memberProfession' => $personProfession];

        return $this->commonService->sendResponse($data, true);
    }
    public function addSecondaryMobile($datas)
    {

        $datas = (object) $datas;
        Log::info('PersonService > addSecondaryMobile function Inside.' . json_encode($datas->mobileNo));
        $checkPrimaryMobile = $this->personInterface->checkPersonByMobileNo($datas->mobileNo);

        $checkMobile = $this->personInterface->checkSecondaryMobileNumberByUid($datas->mobileNo, $datas->personUid);
        if (empty($checkPrimaryMobile) && empty($checkMobile)) {
            $convertMobileNo = $this->convertSecondaryMobileNo($datas);
            $result = $this->personInterface->addSecondaryMobileNoForMember($convertMobileNo);
        } else {

            $result = $checkPrimaryMobile
                ? ['Member' => 'This Number  Exists in Member', 'type' => 2]
                : ['Member' => 'This Number  Exists in Other Member', 'type' => 1];
        }
        return $this->commonService->sendResponse($result, true);
    }
    public function convertSecondaryMobileNo($datas)
    {
        $model = new PersonMobile();
        $model->uid = $datas->personUid;
        $model->country_id = isset($datas->countryId) ? $datas->countryId : null;
        $model->mobile_no = $datas->mobileNo;
        $model->otp_received = $this->sendingOtp();
        $model->mobile_cachet_id = isset($datas->cachetId) ? $datas->cachetId : 2;
        $model->pfm_active_status_id = isset($datas->activeStatusId) ? $datas->activeStatusId : 1;
        return $model;
    }
    public function resendOtpForMobile($datas)
    {

        $datas = (object) $datas;
        $model = $this->personInterface->getPersonMobileNoByUid($datas->uid, $datas->mobile_no);
        if ($model) {
            $otp = $this->sendingOtp();

            $setOtpMobile = $this->setOtpMobileNo($datas->uid, $datas->mobile_no, $otp);
            $data = ['Message' => ' Resend OTP Successfully', 'type' => 1];
        } else {
            $data = ['Message' => 'Data Not Found', 'type' => 2];
        }
        return $this->commonService->sendResponse($data, true);
    }
    public function deleteForMobileNoByUid($datas)
    {
        $datas = (object) $datas;
        Log::info('PersonService > deleteForMobileNoByUid function Inside.' . json_encode($datas));
        $model = $this->personInterface->destroyMobileNoByUid($datas->uid, $datas->mobile_no);
        if ($model) {
            $result = ['Message' => 'MobileNo are Deleted', 'type' => 1];
        } else {
            $result = ['Message' => 'MobileNo Not Found', 'type' => 2];
        }
        return $this->commonService->sendResponse($result, true);
    }
    public function makeAsPrimaryMobileOtpValidate($datas)
    {

        $otpValidate = $this->OtpValidateSecondaryMobileNo($datas);

        $datas = (object) $datas;
        if ($otpValidate == 1) {
            $perviousMobileNo = $this->personInterface->getPerviousPrimaryMobileNo($datas->personUid);
            $setprimaryMobileNo = $this->personInterface->setPirmaryMobileNo($datas);
            $message = ['status' => 'primary changed Successfully', 'type' => 1];
        } else {
            $message = ['status' => 'OTP Validation Failed ', 'type' => 2];
        }

        return $this->commonService->sendResponse($message, true);
    }
    public function OtpValidateSecondaryMobileNo($datas)
    {

        $datas = (object) $datas;

        $checkMobile = $this->personInterface->getSecondaryMobileNoByUid($datas->mobileNo, $datas->personUid);

        if ($checkMobile->otp_received == $datas->otp) {
            $result = $this->personInterface->setStatusForMobileNo($checkMobile->uid, $checkMobile->mobile_no);
        } else {
            $result = ['message' => 'Failed', 'status' => 'OTP validation Failed'];
        }
        return $result;
    }
    public function addSecondaryEmail($datas)
    {
        $datas = (object) $datas;
        $checkPrimaryEmail = $this->personInterface->checkPersonByEmail($datas->email);
        $checkEmail = $this->personInterface->checkSecondaryEmailByUid($datas->email, $datas->personUid);
        if (empty($checkPrimaryEmail) && empty($checkEmail)) {
            $convertEmail = $this->convertSecondaryEmail($datas);

            $result = $this->personInterface->addSecondaryEmailForMember($convertEmail);
        } else {
            $result = $checkPrimaryEmail
                ? ['Member' => 'Email  Exists in Other Member', 'type' => 1]
                : ['Member' => 'Email Exists', 'type' => 2];
        }
        return $this->commonService->sendResponse($result, true);
    }
    public function convertSecondaryEmail($datas)
    {
        $model = new PersonEmail();
        $model->uid = $datas->personUid;
        $model->email = $datas->email;
        $model->otp_received = $this->sendingOtp();
        $model->email_cachet_id = isset($datas->cachetId) ? $datas->cachetId : 2;
        $model->pfm_active_status_id = isset($datas->activeStatusId) ? $datas->activeStatusId : 2;

        return $model;
    }
    public function sendingOtp()
    {
        return random_int(1000, 9999);
    }
    public function resendOtpForEmail($datas)
    {
        $datas = (object) $datas;
        $model = $this->personInterface->checkPersonEmailByUid($datas->email, $datas->uid);
        if ($model) {
            $otp = $this->sendingOtp();
            $setOtpEmail = $this->personInterface->setOtpForPersonPrimaryEmail($datas->uid, $datas->email, $otp);
            $data = ['Message' => ' Resend OTP Successfully', 'type' => 1];
        } else {
            $data = ['Message' => 'Email Not Found', 'type' => 2];
        }
        return $this->commonService->sendResponse($data, true);
    }
    public function deleteForEmailByUid($datas)
    {
        $datas = (object) $datas;
        $model = $this->personInterface->deletedPersonEmailByUid($datas->email, $datas->uid);
        if ($model) {
            $result = ['Message' => 'Email are Deleted', 'type' => 1];
        } else {
            $result = ['Message' => 'Email Not Found', 'type' => 2];
        }
        return $this->commonService->sendResponse($model, true);
    }
    public function makeAsPrimaryEmailOtpValidate($datas)
    {

        $otpValidate = $this->OtpValidateForSecondaryEmail($datas);
        $datas = (object) $datas;
        if ($otpValidate == 1) {
            $perviousEmail = $this->personInterface->getPerviousPrimaryEmail($datas->personUid);
            $setprimaryEmail = $this->personInterface->setPirmaryEmail($datas);
            $result = ['status' => 'primary changed Successfully', 'type' => 1];
        } else {
            $result = ['status' => 'OTP Validation Failed ', 'type' => 2];
        }

        return $this->commonService->sendResponse($result, true);
    }
    public function OtpValidateForSecondaryEmail($datas)
    {
        $datas = (object) $datas;
        $checkEmail = $this->personInterface->getSecondaryEmailByUid($datas->email, $datas->personUid);
        if ($checkEmail->otp_received == $datas->otp) {
            $result = $this->personInterface->personEmailStatusUpdate($checkEmail->uid, $checkEmail->email);
        } else {
            $result = ['message' => 'Failed', 'status' => 'OTP validation Failed'];
        }
        return $result;
    }
    public function resendOtpForSecondaryMobile($datas)
    {
        $datas = (object) $datas;
        $otp = $this->sendingOtp();
        $resendOtp = $this->personInterface->setOtpMobileNo($datas->uid, $datas->number, $otp);
        if ($resendOtp) {
            $result = ['Message' => 'Resend Otp Succesfully', 'type' => 1];
        } else {
            $result = ['Message' => 'Resend  Failed', 'type' => 2];
        }
        return $this->commonService->sendResponse($result, true);
    }
    public function resendOtpForSecondaryEmail($datas)
    {

        $datas = (object) $datas;
        $otp = $this->sendingOtp();
        $resendOtpSecondaryEmail = $this->personInterface->setOtpForPersonPrimaryEmail($datas->uid, $datas->email, $otp);

        if ($resendOtpSecondaryEmail) {
            $result = ['Message' => 'Resend Otp Succesfully', 'type' => 1];
        } else {
            $result = ['Message' => 'email Not  Failed', 'type' => 2];
        }
        return $this->commonService->sendResponse($result, true);
    }
    public function otpValidationForMobile($datas)
    {

        $datas = (object) $datas;
        $checkMobile = $this->personInterface->getMobileNoByUid($datas->mobileNo, $datas->personUid);
        if ($checkMobile->otp_received == $datas->otp) {
            $result = $this->personInterface->setStatusForMobileNo($checkMobile->uid, $checkMobile->mobile_no);
        } else {
            $result = ['message' => 'Failed', 'status' => 'OTP validation Failed'];
        }
        return $result;
    }
    public function findExactPersonWithEmailAndMobile($datas)
    {
        $datas = (object) $datas;
        $email = $datas->email;
        $mobile = $datas->mobileNo;
        $checkMobileAndEmail = $this->personInterface->findExactPersonWithEmailAndMobile($email, $mobile);
        return $this->commonService->sendResponse($checkMobileAndEmail, true);
    }
    public function findMemberDataByUid($uid)
    {
        $checkMember = $this->memberInterface->findMemberDataByUid($uid);
        return $this->commonService->sendResponse($checkMember, true);
    }
    public function getPrimaryMobileAndEmailbyUid($uid)
    {
        $memberPrimaryDatas = $this->personInterface->getPrimaryMobileAndEmailbyUid($uid);
        return $this->commonService->sendResponse($memberPrimaryDatas, true);
    }
    public function personProfileDatas($datas)
    {
        Log::info('PersonService > personProfileDatas function Inside.' . json_encode($datas));
        $datas = (object) $datas;

        $member = $this->personInterface->getAllDatasInMember($datas->uid);
        $personDetails = $member['personDetails'];
        $primaryMobile = $member['mobile'];
        $primaryEmail = $member['email'];
        $profilePic = $member['profilePic'];
        $personGender = $member['personDetails']['gender'];
        $personbloodGroup = $member['personDetails']['bloodGroup'];
        $primaryAddress = isset($member['personAddress']['ParentComAddress']) ? $member['personAddress']['ParentComAddress'] : '';
        $personEducation = $member['personEducation'];
        $personProfession = $member['personProfession'];

        $data = ['memberDeatils' => $personDetails, 'primaryMobile' => $primaryMobile, 'primaryEmail' => $primaryEmail, 'profilePic' => $profilePic, 'memberGender' => $personGender, 'memberBloodGroup' => $personbloodGroup, 'primaryAddress' => $primaryAddress, 'memberEducation' => $personEducation, 'memberProfession' => $personProfession];

        return $this->commonService->sendResponse($data, true);
    }
    public function getPersonMobileNoByUid($datas)
    {
        $datas = (object) $datas;
        $mobile = $this->personInterface->getPersonMobileNoByUid($datas->uid, $datas->mobileNo);
        if ($mobile) {
            return $this->commonService->sendResponse($mobile, true);
        } else {
            return $this->commonService->sendError('MobileNo Not Found', false);
        }
    }
    public function getPersonPrimaryDataByUid($uid)
    {
        $personPrimaryDatas = $this->personInterface->getPersonPrimaryDataByUid($uid);
        return $this->commonService->sendResponse($personPrimaryDatas, true);
    }
    public function personMotherTongueByUid($uid)
    {
        $model = $this->personInterface->personMotherTongueByUid($uid);
        return $this->commonService->sendResponse($model, true);
    }
    public function personGetAnniversaryDate($uid)
    {
        $model = $this->personInterface->personGetAnniversaryDate($uid);
        return $this->commonService->sendResponse($model, true);
    }
    public function personAddressByUid($uid)
    {
        $model = $this->personInterface->personAddressByUid($uid);
        return $this->commonService->sendResponse($model, true);
    }
    public function getPersonEmailByUidAndEmail($datas)
    {
        $datas = (object) $datas;
        $model = $this->personInterface->getPersonEmailByUidAndEmail($datas->uid, $datas->email);
        if ($model) {
            return $this->commonService->sendResponse($model, true);
        } else {
            return $this->commonService->sendError('email Not Found', false);
        }
    }
}
