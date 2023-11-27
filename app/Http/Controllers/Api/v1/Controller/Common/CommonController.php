<?php

namespace App\Http\Controllers\Api\v1\Controller\Common;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\v1\Service\Common\CommonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommonController extends Controller
{
    protected $CommonService;

    public function __construct(CommonService $commonService)
{
    $this->commonService = $commonService;
}
public function getSalutation()
{
    $response = $this->commonService->getSalutation();
    Log::info('PersonController > getSalutation function Return.' . json_encode($response));
    return $response;
}
}
