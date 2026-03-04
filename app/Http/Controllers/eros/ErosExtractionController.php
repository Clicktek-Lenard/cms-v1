<?php

namespace App\Http\Controllers\eros;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Maatwebsite\Excel\Facades\Excel;

use App\Exports\eros\CensusExport;

use App\Http\Controllers\Controller;


class ErosExtractionController extends Controller
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

   
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    //{
//	 return 'ricky';//view('eros.physicianListCreate');
  //  }

	public function ErosGetCompanyTransaction()
	{
		/*$datas = DB::connection('ErosDump')->table('BILLING_TRX_HDR')->where('BTH_TRXDT', '<=', '2023-07-05%')->where('BTH_TRXDT', '>=', '2022-07-05%')
		->leftJoin('COMPANY_DETAILS','BILLING_TRX_HDR.BTH_COMPANY','=','COMPANY_DETAILS.CD_CODE')
		->leftJoin('CLINICIAN_DETAILS','BILLING_TRX_HDR.BTH_CLINICIAN','=','CLINICIAN_DETAILS.CD_CODE')
		->limit(1000)
		->get(array('BILLING_TRX_HDR.BTH_TRXNO', 'BILLING_TRX_HDR.BTH_TRXDT', 'BILLING_TRX_HDR.BTH_PID', 'BILLING_TRX_HDR.BTH_COMPANY','COMPANY_DETAILS.CD_NAME as CNAME', 'BTH_CLINICIAN', 'CLINICIAN_DETAILS.CD_NAME as PNAME'));
		*/
		return Excel::download(new CensusExport('2'), 'Extraction All.xlsx');
		
		
	}
   
}
