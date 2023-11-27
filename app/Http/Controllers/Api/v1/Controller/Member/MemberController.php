<?php

namespace App\Http\Controllers\Api\v1\Controller\Member;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\v1\Service\Member\MemberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MemberController extends Controller
{

    protected $MemberService;

    public function __construct(MemberService $MemberService)
    {
        $this->MemberService = $MemberService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function storeMember(Request $request)
    {
        Log::info('MemberController > storeMember function Inside.' . json_encode($request->all()));
        $response = $this->MemberService->storeMember($request->all());
        Log::info('MemberController > storeMember function Return.' . json_encode($response));
        return $response;
    }
    public function setPasswordForMember(Request $request)
    {
        Log::info('MemberController > setPasswordForMember function Inside.' . json_encode($request->all()));
        $response = $this->MemberService->setPasswordForMember($request->all());
        Log::info('MemberController > setPasswordForMember function Return.' . json_encode($response));
        return $response;
    }
    public function passwordUpdateForMember(Request $request)
    {
        Log::info('MemberController > passwordUpdateForMember function Inside.' . json_encode($request->all()));
        $response = $this->MemberService->passwordUpdateForMember($request->all());
        Log::info('MemberController > passwordUpdateForMember function Return.' . json_encode($response));
        return $response;
    }
    public function memberLogin(Request $request)
    {

        Log::info('MemberController > memberLogin function Inside.' . json_encode($request->all()));
        $response = $this->MemberService->memberLogin($request->all());
        Log::info('MemberController > memberLogin function Return.' . json_encode($response));
        return $response;
    }
    public function memberCreation(Request $request)
    {
        Log::info('MemberController > memberCreation function Inside.' . json_encode($request->all()));
        $response = $this->MemberService->memberCreation($request->all());
        Log::info('MemberController > memberCreation function Return.' . json_encode($response));
        return $response;
    }
    public function memberLogout(Request $request)
    {
    
        Log::info('MemberController > memberLogout function Inside.' . json_encode($request->all()));
        $response = $this->MemberService->memberLogout();
        Log::info('MemberController > memberLogout function Return.' . json_encode($response));
        return $response;
    }
}
