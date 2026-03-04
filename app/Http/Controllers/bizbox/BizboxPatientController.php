<?php

namespace App\Http\Controllers\bizbox;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Maatwebsite\Excel\Facades\Excel;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

use App\Exports\Bizbox\BizboxPatientExport;



class BizboxPatientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
	return view('cms.reports.dailyCensus');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
	
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
    
		
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
	return Excel::download(new BizboxPatientExport(date('Y-m-d')), 'Patient.xlsx');
	
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
	  
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
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
