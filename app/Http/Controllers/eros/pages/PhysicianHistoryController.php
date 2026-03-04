<?php

namespace App\Http\Controllers\eros\pages;                       //pcp v2

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Database\Query\Builder;
use App\Models\eros\ErosDB;
use App\Models\cms\Queue;


class PhysicianHistoryController extends Controller
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
    $physicianDatas = DB::connection('Eros')->table('Physician')
        ->where('Id', '=', $id)
        ->get(['Id', 'FullName', 'Code', 'ApprovalLogs', 'SystemUpdateTime', 'UpdateBy', 'Status']);
    //dd($physicianDatas);
    $physicianAudit = DB::connection('Audit')->table('ErosPhysicianInfo')->where('IdPhysician', '=', $id)->get(['Id', 'Logs', 'SystemDateTime']);


    return view('eros.pages.physicianHistory', ["physicianDatas" => $physicianDatas, "physicianAudit" => $physicianAudit]);
}


}
