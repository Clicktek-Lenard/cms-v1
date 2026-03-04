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

class PhysicianApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     *     
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      
    }
    
    public function store(Request $request, $id)
    {
	//
    }


    public function edit(Request $request, $id)
    {
        $physicianViewDatas = ErosDB::getPhysicianData($id); //data from physicianInfoView.blade.php
    //    dd($physicianViewDatas);
        return view('cms.pages.physicianInfoView', ['physicianViewDatas' => $physicianViewDatas]);
    }

    public function show(Request $request,$id)
    {
        //show
    }
    

    public function physicianApproval(Request $request) //updated
    {
        $idphysician = $request->input('idphysician');
	//dd($idphysician);
	DB::connection('CMS')->beginTransaction();

            DB::connection('CMS')->table('Eros.Physician')
            ->where('Id', $idphysician)
            ->update([
                'Status'        => 'RP - Leads',
                'UpdateDate'	=> date('Y-m-d'),
                'ApproveBy'	    => Auth::user()->username
            ]);
	
	
	$listPhysicianQue = DB::connection('CMS')->table('Queue')
            ->leftjoin('Transactions', 'Transactions.IdQueue', '=', 'Queue.Id')
	    ->where('Transactions.IdDoctor', '=', $idphysician)
	    ->whereIn('Queue.Status', ['212', '213'])
	    ->groupBy('Queue.Id')
	    ->get(array('Queue.Id')); 
	    
	DB::connection('CMS')->table('Transactions')
           // ->where('IdQueue', $_GET['idQueue'])
	    ->where('IdDoctor', '=', $idphysician)
	    ->whereIn('Status', ['212', '213'])
            ->update([
                'Status' => 214
        ]); 
	    
	foreach($listPhysicianQue as $que)
	{
		DB::connection('CMS')->table('Queue')->where('Id', '=', $que->Id)->update(['Status' => 214]);
	}
	
	
	DB::connection('CMS')->commit();  

    }

    
 
}
