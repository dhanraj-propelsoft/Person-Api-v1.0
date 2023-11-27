<?php


namespace App\Http\Controllers\Api\v1\Interface\Member;

interface MemberInterface
{
    public function findMemberByMobileNo($mobileNo);
    // public function findUserDataByEmail($data);
    public Function storeMember($model);
    public function findMemberDataByUid($uid);
    public function verifyMemberForMobile($data);
}
