<?php

namespace App\Http\Controllers\Api\v1\Interface\Common;

interface SmsInterface
{
    public function store($model);
    public function findSmsTypeByName($name);
}
