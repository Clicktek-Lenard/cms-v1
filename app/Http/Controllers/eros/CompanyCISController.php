<?php

namespace App\Http\Controllers\eros;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use App\Models\eros\ErosDB;

use App\Models\eros\ItemPrice;



class CompanyCISController extends Controller
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
	if( ! $checking = $this->CheckModuleAccess() )
	{
		return response(['error' => ['code' => 'INSUFFICIENT ROLE for ',	'description' => 'You are not authorized to access this resource.']], 401);
	}

        $request->validate([
            'file' => 'required|mimes:xlsx|max:2048',
        ]);
  
        $fileName = time().'.'.$request->file->extension();  
        $request->file->move(public_path('uploads/Company'), $fileName);

	
	DB::connection('Eros')->beginTransaction();
	$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('uploads/Company')."/".$fileName);
	//  Get worksheet dimensions
	$sheet = $spreadsheet->getActiveSheet(); 
	$highestRow = $sheet->getHighestRow(); 
	$highestColumn = $sheet->getHighestColumn();
	//  Loop through each row of the worksheet in turn
	for ($row = 3; $row <= $highestRow; $row++)
	{ 
		//  Read a row of data into an array
		$rowData = $sheet->rangeToArray('A' . $row . ':D' . $row,NULL,TRUE,FALSE);
		if(!empty($rowData[0][1]) && !empty($rowData[0][2]) )
		{
			$checkIfExsit = ItemPrice::getItemByCompany(array('CompanyCode' =>$request->input('_erosCode'), 'Code' =>$rowData[0][2]  ));
			
			//echo count($checkIfExsit)."<BR>";
			//echo $checkIfExsit[0]->Id;
			
			//die();
			if(count($checkIfExsit) ==0 )
			{
				ItemPrice::insertItemPrice(array(
					'Code'		=> $rowData[0][2]
					,'Description'	=> $rowData[0][3]
					,'CompanyCode'	=> $request->input('_erosCode')
					,'Price'		=> $rowData[0][1]
					,'InputDate'	=> date('Y-m-d')	
					,'InputBy'		=> Auth::user()->username
					,'PriceGroup'	=> 'Item'
				));
				
			}else{
				ItemPrice::updateItemPrice(array(
					'Id'			=> $checkIfExsit[0]->Id
					,'Code'		=> $rowData[0][2]
					,'Description'	=> $rowData[0][3]
					,'CompanyCode'	=> $request->input('_erosCode')
					,'Price'		=> $rowData[0][1]
					,'InputDate'	=> date('Y-m-d')	
					,'InputBy'		=> Auth::user()->username
					,'ErosStatus'	=> 'reUpdate'
					,'CebuStatus'	=> 'reUpdate'
				));
				
			}
		
		}
	}
	DB::connection('Eros')->commit(); 
	
	return back()
            ->with('success','You have successfully upload file.')
            ->with('file',$fileName);
	//Excel::download(new PatientExport($fileName), 'IMD Patient - Eros.xlsx');
	
	
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
	return view('eros.companyCIS', ['datas' => $datas, 'postLink' => url(session('userBU').'/erosui/company/cis/'.$id)]); 
	
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
	if( ! $checking = $this->CheckModuleAccess() )
	{
		return response(['error' => ['code' => 'INSUFFICIENT ROLE for ',	'description' => 'You are not authorized to access this resource.']], 401);
	}
	
    }
    
    
   
    
    
}
