<?php

namespace App\Http\Controllers\cms\pages\queue;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Database\Query\Builder;
use App\Models\eros\ErosDB;
use App\Models\cms\Queue;

class PhysicianDeclineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return view('cms/pages.physicianDeclineModal', ['postLink'=>url(session('userBUCode').'/cms/queue/physicianDeclineModal/'.$id)]);
    }
    
    public function store(Request $request, $id)
    {
     //
    }


    public function edit(Request $request, $id)
    {
        $physicianViewDatas = ErosDB::getPhysicianData($id); //data from physicianInfoView.blade.php
       
        return view('cms.pages.physicianDeclineModal', ['physicianViewDatas' => $physicianViewDatas]);
    }

    public function show(Request $request,$id)
    {
        
    }
    
    public function physicianDecline(Request $request)
    {
       
        $physicianId = $request->input('physicianId');
        $idTransactions = $request->input('idQueue');
        $idQueue = $request->input('idQueue');
        $reasons = $request->input('reason', []);
        $declineReason = implode(', ', $reasons);
        // dd($idQueue);
    DB::connection('CMS')->beginTransaction();
       DB::connection('Eros')->table('Physician')
            ->where('Id', $physicianId)
            ->update([
            'Status'        => 'RP - For Revision',
            'DeclineReason'     => $declineReason
        ]);
        // dd($_GET['idQueue']);
        DB::connection('CMS')->table('Transactions')
            ->where('IdQueue', $idTransactions)
            ->where('IdQueue', $_GET['idQueue'])
            ->update([
                'Status' => 213
        ]);

        DB::connection('CMS')->table('Queue')
        ->where('Id', $_GET['idQueue'])
        ->update([
            'Status' => 213
    ]);
    DB::connection('CMS')->commit();
    return response()->json(['message' => 'Physician declined successfully.']);
    }
 
}
