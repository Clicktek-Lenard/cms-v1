<?php

namespace App\Http\Controllers\eros;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use App\Models\eros\ErosDB;

use DataTables;

class PatientServerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

   
    public function index()
    {
	// return route('erosserver.PatientList');
       return view('eros.patientListServer');
	
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
	$datas = ErosDB::getPatientServer(array('byId'=>$id));
	return view('eros.patientListServerEdit', ['datas' => $datas, 'postLink' => url(session('userBUCode').'/erosPatientServer/'.$id)]);    
    }
    
    
    public function getPatientList(Request $request)
    {
    
	$search_arr = $request->get('search');
	$searchValue = $search_arr['value'];

	if ($request->ajax()) {
		$model = ErosDB::getPatientServer(array('fullname'=>$searchValue));
		return DataTables::of($model)->toJson();
	}
    
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
	$datas = ErosDB::getPatientServer(array('AddPatientTemp'=>true,'byId'=>$id, 'transaction' => $request->input('transaction') ));
	
		
	return back()
            ->with('success','You have successfully create Bizbox record.');
           // ->with('file',$fileName);
    }
    
    
     
    
    

}
