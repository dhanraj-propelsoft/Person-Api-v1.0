<?php

namespace App\Http\Controllers\Api\v1\Repositories\common;

use App\Http\Controllers\Api\v1\Interface\Common\SmsInterface;
use App\Models\SmsType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class smsRepository implements SmsInterface
{

    public function findSmsTypeByName($name)
    {

      return SmsType::where('name',$name)->first();
    }
    public function store($model)
    {

        try {
            $result = DB::transaction(function () use ($model) {

                $model->save();
                return [
                    'message' => "Success",
                    'data' => $model
                ];
            });

            return $result;
        } catch (\Exception $e) {


            return [

                'message' => "failed",
                'data' => $e
            ];
        }
    }
}
