<?php

namespace App\Http\Controllers\Api\v1\Repositories\Member;

use App\Http\Controllers\Api\v1\Interface\Member\MemberInterface;
use App\Models\Person;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

class MemberRepository implements MemberInterface
{
    public function storeMember($model)
    {

        try {
            $result = DB::transaction(function () use ($model) {

                $model->save();
                return [
                    'message' => "Success",
                    'data' => $model,
                ];
            });

            return $result;
        } catch (\Exception $e) {

            return [

                'message' => "failed",
                'data' => $e,
            ];
        }
    }
    public function findMemberDataByUid($uid)
    {
        return Member::where('uid', $uid)->whereNull('deleted_at')->first();
    }
    public function verifyMemberForMobile($datas)
    {
        return  Member::where('primary_email', $datas->memberName)
        ->orWhere('primary_mobile', $datas->memberName)
        ->first();

    }
    public function findMemberByMobileNo($mobileNo)
    {
       return Member::with('personDetails')->where('primary_mobile', $mobileNo)->whereNull('deleted_at')->first();
    }
}
