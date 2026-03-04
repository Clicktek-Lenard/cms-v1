<?php

namespace App\Http\Controllers\cms\api;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Http\Controllers\hl7\BAEHL7Controller;


class hPDFController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function reGeneratePDF(Request $request)
    {
        if(strpos(session('userRole'), '"ldap_role":"[RESULTS-RELEASING]"') === false)
	{
		return response(['error' => ['code' => 'INSUFFICIENT ROLE for ',	'description' => 'You are not authorized to access this resource.']], 401);
	}
	
	$idQueue =  $request->input('idQueue');
	
	BAEHL7Controller::createJsonFile4HCpdf($idQueue);
	
	return "Submitted";
	
    }
     
    public function index()
    {
        if(strpos(session('userRole'), '"ldap_role":"[RESULTS-RELEASING]"') === false)
	{
		return response(['error' => ['code' => 'INSUFFICIENT ROLE for ',	'description' => 'You are not authorized to access this resource.']], 401);
	}
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(strpos(session('userRole'), '"ldap_role":"[RESULTS-RELEASING]"') === false)
	{
		return response(['error' => ['code' => 'INSUFFICIENT ROLE for ',	'description' => 'You are not authorized to access this resource.']], 401);
	}
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(strpos(session('userRole'), '"ldap_role":"[RESULTS-RELEASING]"') === false)
	{
		return response(['error' => ['code' => 'INSUFFICIENT ROLE for ',	'description' => 'You are not authorized to access this resource.']], 401);
	}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
	if(strpos(session('userRole'), '"ldap_role":"[RESULTS-RELEASING]"') === false)
	{
		return response(['error' => ['code' => 'INSUFFICIENT ROLE for ',	'description' => 'You are not authorized to access this resource.']], 401);
	}
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
	if(strpos(session('userRole'), '"ldap_role":"[RESULTS-RELEASING]"') === false)
	{
		return response(['error' => ['code' => 'INSUFFICIENT ROLE for ',	'description' => 'You are not authorized to access this resource.']], 401);
	}
	$dataTime = date('YmdHis');
	$pdfData =   DB::connection('CMS')->table('Queue')
				->where('Queue.Id', '=', $id)
				->where('AccessionNo.IdTransaction', '=', $_GET['transid'])
				->whereNotnull('AccessionNo.AccessionNo')
				->leftjoin('AccessionNo', 'AccessionNo.IdQueue', '=', 'Queue.Id' )
				->leftjoin('Eros.Patient', 'CMS.Queue.IdPatient', '=', 'Eros.Patient.Id' )
				->groupBy('AccessionNo.AccessionNo')
				->limit(1)
				->get(array('CMS.AccessionNo.AccessionNo','CMS.Queue.Date','Eros.Patient.Code'))[0];
	$folderDate = str_replace('-','',$pdfData->Date);
	
	$fileName = $pdfData->AccessionNo.'_'.$folderDate.'_'.$pdfData->Code;
	
	$ccs =  '<style> .modal-dialog { width: 90% !important; height:100% !important; }</style> ';
	
	return  $ccs. '<iframe width="100%"  height="600px" src="https://'.$_SERVER['SERVER_NAME'].'/PDF/hPDF/'.$folderDate.'/'.$fileName.'.pdf?'.$dataTime.'" title="PDF Results"></iframe>';
	
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
        if(strpos(session('userRole'), '"ldap_role":"[RESULTS-RELEASING]"') === false)
	{
		return response(['error' => ['code' => 'INSUFFICIENT ROLE for ',	'description' => 'You are not authorized to access this resource.']], 401);
	}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(strpos(session('userRole'), '"ldap_role":"[RESULTS-RELEASING]"') === false)
	{
		return response(['error' => ['code' => 'INSUFFICIENT ROLE for ',	'description' => 'You are not authorized to access this resource.']], 401);
	}
    }
}
