<?php

namespace App\Http\Controllers\cms;

use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use GravityMedia\Ghostscript\Ghostscript;
use Symfony\Component\Process\Process;

use App\Http\Controllers\Controller;
use App\Models\eros\ErosDB;

use App\Models\eros\ItemPrice;

use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use setasign\Fpdi\Tcpdf\Fpdi;

use DataTables;


class QueueCISViewController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       
    }

   
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
	public function create()
	{
		//return 'ricky';//view('eros.physicianListCreate');
	}

	public function index()
	{
		
	}
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
      
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
       
        $datas = ErosDB::getCompanyData($id);
        $CISData = DB::connection('Eros')->table('ItemPriceCIS')->where('CompanyId', $id)->get();
        // dd($CISData);
        return view('cms/pages.queueCISView', ['datas' => $datas, 'CISData' => json_encode($CISData),  'postLink' => url('/erosui/company/cisview/'.$id)]); 
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
    
    
   
    
    
}
