<?php

namespace App\Http\Controllers\Api\v1\Service\Member;

use App\Http\Controllers\Api\v1\Interface\Member\MemberInterface;
use App\Http\Controllers\Api\v1\Interface\Person\PersonInterface;
use App\Http\Controllers\Api\v1\Service\Common\CommonService;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MemberService
{
    public function __construct(MemberInterface $MemberInterface, PersonInterface $personInterface, CommonService $commonService)
    {
        $this->MemberInterface = $MemberInterface;
        $this->personInterface = $personInterface;
        $this->commonService = $commonService;
    }
    public function storeMember($data)
    {

        Log::info('MemberService > storeMember function Inside.' . json_encode($data));
        $validator = Validator::make($data, [
            'password' => 'required|string|max:255',
            'passwordConfirmation' => 'required|string|same:password',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $datas = (object) $data;
        $personModel = $this->personInterface->getPrimaryMobileAndEmailbyUid($datas->uid);
        $personData = ['mobile' => $personModel->mobile->mobile_no, 'email' => $personModel->email->email];
        $model = $this->convertToMemberModel($personData, $datas);
        $storeMember = $this->MemberInterface->storeMember($model);
        Log::info('MemberService > storeMember function Return.' . json_encode($storeMember));

        if ($storeMember['message'] == "Success") {

            return $this->commonService->sendResponse($storeMember['data'], $storeMember['message']);
        } else {
            return $this->commonService->sendError($storeMember['data'], $storeMember['message']);
        }
    }
    public function convertToMemberModel($personData, $datas)
    {
        Log::info('MemberService -> convertToMemberModel  function Inside.' . json_encode($datas));
        Log::info('MemberService -> convertToMemberModel  function Inside.' . json_encode($personData));
        $personData = (object) $personData;
        $model = new Member();
        $model->uid = $datas->uid;
        $model->primary_email = $personData->email;
        $model->primary_mobile = $personData->mobile;
        $model->password = Hash::make($datas->password);
        $model->pfm_stage_id = 1;
        $model->pfm_active_status_id = 1;
        Log::info('MemberService > convertToMemberModel function Return.' . json_encode($model));
        return $model;
    }
    public function setPasswordForMember($datas)
    {
        $validator = Validator::make($datas, [
            'password' => 'required|string|max:255',
            'passwordConfirmation' => 'required|string|same:password',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        Log::info('MemberService > setPasswordForMember function Inside.' . json_encode($datas));
        $datas = (object) $datas;
        $member = $this->MemberInterface->findMemberDataByUid($datas->uid);
        Log::info('MemberService > setPasswordForMember function Return.' . json_encode($member));
        if ($member) {
            $password = Hash::make($datas->password);
            $member->password = $password;
            $model = $this->MemberInterface->storeMember($member);
            if ($model['message'] == "Success") {
                $memberModel = $model['data'];
                $personModel = $this->personInterface->getPersonPrimaryDataByUid($memberModel->uid);
                return $this->commonService->sendResponse($personModel, $model['message']);
            } else {
                return $this->commonService->sendError($model['data'], $model['message']);
            }
        }

    }
    public function passwordUpdateForMember($datas)
    {
        $validator = Validator::make($datas, [
            'password' => 'required|string|max:255',
            'passwordConfirmation' => 'required|string|same:password',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        Log::info('MemberService > passwordUpdateForMember function Inside.' . json_encode($datas));
        $datas = (object) $datas;
        $member = $this->MemberInterface->findMemberDataByUid($datas->uid);
        Log::info('MemberService > passwordUpdateForMember function Return.' . json_encode($member));
        if (Hash::check($datas->oldPassword, $member->password)) {
            $password = Hash::make($datas->password);
            $member->password = $password;
            $model = $this->MemberInterface->storeMember($member);
        } else {
            $model = ['message' => 'Failed', 'status' => 'old Password MisMatched'];
        }
        return $this->commonService->sendResponse($model, '');
    }
    public function memberLogin($datas)
    {
        $validator = Validator::make($datas, [
            'memberName' => 'required|string|max:255',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        Log::info('MemberService > memberLogin function Inside.' . json_encode($datas));
        $datas = (object) $datas;

        $verifyMember = $this->MemberInterface->verifyMemberForMobile($datas);

        Log::info('MemberService > memberLogin function Return.' . json_encode($verifyMember));
        if ($verifyMember) {
            $uid = $verifyMember->uid;
            $personDetail = $this->personInterface->getPersonPicAndPersonName($uid);
            $nickName = $personDetail->nick_name ?? null;
            $firstName = $personDetail->first_name ?? null;
            $personPic = $personDetail->PersonPic->profile_pic ?? null;
            if (Hash::check($datas->password, $verifyMember->password)) {
                $token = $verifyMember->createToken('Laravel Password Grant Client')->accessToken;

                $personStatus = $this->personInterface->checkPersonExistence($uid);
                $personType = $personStatus ? $personStatus->existence : null;
                $response = ['type' =>1, 'personType' => $personType, 'token' => $token, 'uid' => $uid,'nickName' => $nickName, 'firstName' => $firstName, 'personPic' => $personPic];
                return $this->commonService->sendResponse($response, "Login Successfully");
            } else {
                $response = ['type' =>2, 'firstName' => $firstName, 'uid' => $uid];
                return $this->commonService->sendError($response, "Password Invalid");
            }
        } else {
            $response = ["message" => 'Member does not exist'];

            return response($response, 422);
        }
    }
    public function memberCreation($datas)
    {

        Log::info('MemberService > memberCreation function Inside.' . json_encode($datas));
        $datas = (object) $datas;
        $mobile = $this->personInterface->getPrimaryMobileNumberByUid($datas->uid);
        $email = $this->personInterface->getPersonEmailByUid($datas->uid);
        $getPersonName = $this->personInterface->getPersonDatasByUid($datas->uid);
        $createMember = $this->memberCreate($mobile->mobile_no, $email->email, $datas);
        $saveMember = $this->MemberInterface->storeMember($createMember);
        $result = ['personName' => $getPersonName->first_name, 'mobileNumber' => $mobile->mobile_no];
        Log::info('MemberService > memberCreation function Return.' . json_encode($result));
        return $this->commonService->sendResponse($result, '');
    }
    public function memberCreate($mobile, $email, $datas)
    {
        Log::info('MemberService > MemberCreate function Inside.' . json_encode($datas));
        $model = $this->MemberInterface->findMemberDataByUid($datas->uid);
        if ($model) {
            $model->uid = $datas->uid;
        } else {
            $model = new Member();
            $model->uid = $datas->uid;
        }
        $model->primary_email = $email;
        $model->primary_mobile = $mobile;
        $model->password = Hash::make($datas->password);
        $model->pfm_stage_id = 1;
        Log::info('MemberService > MemberCreate function Return.' . json_encode($model));
        return $model;
    }
    public function memberLogout()
    {
        $token = auth()->user()->token();
        $token->revoke();
        $result = ['type'=>1,'message' => ' Logged Out Successfully!'];
        return $this->commonService->sendResponse($result, '');
    }
}
