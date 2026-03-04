<?php

namespace App\Http\Controllers\cms\doctors;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\cms\Queue;
use App\Models\eros\ErosDB;
use Carbon\Carbon;
class DoctorTempController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        dd('index test');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
	DB::connection('CMS')->beginTransaction();
        $docIn = DB::connection('Eros')->table('Physician')->where('EmpId', '=', Auth::user()->AccessMapId)->get(array('Id'));
	
	if(count($docIn) == 0)
	{
		return "Missing Physician Mapping";
		die();
	}
    
        // Check if the record exists in SOAPTemp for the given IdQueue
        $existingRecordOnTemp = DB::connection('CMS')->table('SOAPTemp')->where('QueueCode', $request->input('_queueCode'))->where('IdDoctor', '=', $docIn[0]->Id)->first();
        ///dd(isset($existingRecord));
        $existingRecordOnSOAP = DB::connection('CMS')->table('SOAP')->where('QueueCode', $request->input('_queueCode'))->where('IdDoctor', '=', $docIn[0]->Id)->first();
       
        if (isset($existingRecordOnSOAP)) 
        {
            $SOAP = DB::connection('CMS')->table('SOAP')->where('QueueCode', $request->input('_queueCode'))->get(['*']);
            foreach($SOAP as $data){
                DB::connection('CMS')->table('SOAPTemp')->insert([
                    'QueueCode'          => $data->QueueCode
                    ,'IdPatient'         	=> $data->IdPatient
                    ,'IdDoctor'          	=> $docIn[0]->Id
                    ,'NameDoctor'        => $data->NameDoctor
                    ,'Subjective'       	=> $request->input('subjective')?: $data->Subjective
                    ,'Objective'         	=> $request->input('objective') ?: $data->Objective
                    ,'Assessment'        	=> $request->input('assessment') ?: $data->Assessment
                    ,'Plan'             	=> $request->input('plan') ?: $data->Plan
		    ,'InputBy'		=> Auth::user()->username
		    ,'InputDate'		=> date('Y-m-d')
                    ,'Status'           	=> $data->Status
                ]);
                DB::connection('CMS')->table('SOAP')->where('QueueCode', $request->input('_queueCode'))->where('IdDoctor', '=', $docIn[0]->Id)->delete();
             }
        } 
        else if(isset($existingRecordOnTemp))
        {

            DB::connection('CMS')->table('SOAPTemp')->where('QueueCode', $request->input('_queueCode'))->where('IdDoctor', '=', $docIn[0]->Id)->update([
                'Subjective' 	=> $request->input('subjective') ?: $existingRecordOnTemp->Subjective
                ,'Objective'  	=> $request->input('objective') ?: $existingRecordOnTemp->Objective
                ,'Assessment' 	=> $request->input('assessment') ?: $existingRecordOnTemp->Assessment
                ,'Plan'       	=> $request->input('plan') ?: $existingRecordOnTemp->Plan
		,'IdPatient'   	=> $request->input('IdPatient')
                ,'IdDoctor'   	=> $docIn[0]->Id
                ,'NameDoctor'	=> '0'
                ,'Status'     	=> '4'
		,'InputBy'		=> Auth::user()->username
		,'InputDate'	=> date('Y-m-d')
            ]);
        }
        else
        {
            // Insert a new record if it doesn't exist
            DB::connection('CMS')->table('SOAPTemp')->insert([
                'QueueCode'    	=> $request->input('_queueCode')
                ,'Subjective' 	=> $request->input('subjective') ?: ''
                ,'Objective'  	=> $request->input('objective') ?: ''
                ,'Assessment' 	=> $request->input('assessment') ?: ''
                ,'Plan'       	=> $request->input('plan') ?: ''
		,'IdPatient'   	=> $request->input('IdPatient')
                ,'IdDoctor'   	=> $docIn[0]->Id
                ,'NameDoctor' 	=> '0'
                ,'Status'     	=> '4'
		,'InputBy'		=> Auth::user()->username
		,'InputDate'	=> date('Y-m-d')
            ]);
        }
       DB::connection('CMS')->commit(); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return $id;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {  
         //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */

}
