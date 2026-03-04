<?php

namespace App\Http\Controllers\eros\pages;                          //pcp v2

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Database\Query\Builder;
use App\Models\eros\ErosDB;
use App\Models\cms\Queue;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendingPhysicianNotification;

class DoctorModalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('jampol');
    }
    
    public function store(Request $request, $id)
    {
     //
    }


    public function edit(Request $request, $id)
    {
        $doctorsDatas = ErosDB::getDoctorData($id); //data from physicianInfoView.blade.php
            //dd($doctorsDatas);
        return view('eros.pages.declineModal', ['doctorsDatas' => $doctorsDatas]);
    }

  
    public function approvalDoctor(Request $request)
    {
        $doctorId = $request->input('doctorId');

        DB::connection('Eros')
        ->table('Physician')
        ->where('Id', $doctorId)
        ->update([
            'Status' => 'Approved',
            'ApprovalLogs' => NULL,
            'DeclineReason' => NULL,
            'SystemUpdateTime' => now(),
            'ApproveBy' =>  Auth::user()->username            

        ]);

        return response()->json(['message' => 'Doctor approved successfully']);     
    }
    
    public function declineDoctor(Request $request)
    {
        $doctorId = $request->input('doctorId');
        $reasons = $request->input('reasons');
     
        DB::connection('Eros')
        ->table('Physician')
        ->where('Id', $doctorId)
        ->update([
            'Status' => 'Disapproved',
            'DeclineReason' => $reasons    

        ]);

        return response()->json(['message' => 'Doctor for Revision']);     
    }


 
}
