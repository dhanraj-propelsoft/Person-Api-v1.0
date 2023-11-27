<?php

namespace App\Http\Controllers\Api\v1\Interface\Common;

interface CommonInterface
{
    public function getSalutation();
    public function getAllGender();
    public function getAllBloodGroup();
    public function getAllStates();
    public function getAddrerssType();
    public function getMaritalStatus();
    public function getLanguage();
    public function getCityByStateId($stateId);
    public function getAllDocumentType();
    public function getAllBankAccountType();
}
