<?php

namespace App\Http\Controllers\Api\v1\Repositories\common;

use App\Http\Controllers\Api\v1\Interface\Common\CommonInterface;
use App\Models\Address_of;
use App\Models\BasicModels\BankAccountType;
use App\Models\BasicModels\BloodGroup;
use App\Models\BasicModels\City;
use App\Models\BasicModels\DocumentType;
use App\Models\BasicModels\Gender;
use App\Models\BasicModels\Language;
use App\Models\BasicModels\MaritalStatus;
use App\Models\BasicModels\Salutation;
use App\Models\BasicModels\State;
use Illuminate\Support\Facades\Log;

class CommonRepository implements CommonInterface
{
    public function getSalutation()
    {
        return Salutation::whereNull('deleted_at')->get();
    }

    public function getAllGender()
    {
        return Gender::whereNull('deleted_at')->get();

    }
    public function getAllBloodGroup()
    {
        return BloodGroup::whereNull('deleted_at')->get();

    }
    public function getCityByStateId($stateId)
    {
        return City::where('state_id', $stateId)->whereNull('deleted_at')->get()->toArray();

    }
    public function getAllStates()
    {

        return State::whereNull('deleted_at')->get();
    }
    public function getAddrerssType()
    {
        return Address_of::whereNull('deleted_at')->get();

    }
    public function getMaritalStatus()
    {
        return MaritalStatus::whereNull('deleted_at')->get();
    }
    public function getLanguage()
    {
        return Language::whereNull('deleted_at')->get();
    }
    public function getAllDocumentType()
    {
        return DocumentType::whereNull('deleted_at')->get();
    }
    public function getAllBankAccountType()
    {
        return BankAccountType::whereNull('deleted_at')->get();
    }

}
