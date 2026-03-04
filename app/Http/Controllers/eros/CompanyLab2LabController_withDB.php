<?php

/*
SELECT tb2.`Description`,tb2.`Code`, tb1.*  FROM `ItemPrice` tb1
LEFT JOIN `ItemMaster` tb2 ON (tb1.`Code` = tb2.`Code`)
WHERE tb1.`Description` IS NULL and tb2.`Description` IS NOT NULL

UPDATE `ItemPrice` tb1
LEFT JOIN `ItemMaster` tb2 ON (tb1.`Code` = tb2.`Code`)
SET tb1.`Description` = tb2.`Description`
WHERE tb1.`Description` IS NULL and tb2.`Description` IS NOT NULL

*/


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



class CompanyLab2LabController extends Controller
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

	
	//DB::connection('Eros')->beginTransaction();
	$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('uploads/Company')."/".$fileName);
	//  Get worksheet dimensions
	$sheet = $spreadsheet->getActiveSheet(); 
	$highestRow = $sheet->getHighestRow(); 
	$highestColumn = $sheet->getHighestColumn();
	//  Loop through each row of the worksheet in turn
	
$oldCompany =  DB::connection('Eros')->select("SELECT * FROM `Company` WHERE `reUpload` LIKE 'reUpdate' LIMIT 50  ");
foreach($oldCompany as $old)
{ 
	for ($row = 3; $row <= $highestRow; $row++)
	{ 
		//  Read a row of data into an array
		$rowData = $sheet->rangeToArray('A' . $row . ':D' . $row,NULL,TRUE,FALSE);
		if(!empty($rowData[0][1]) && !empty($rowData[0][2]) )
		{
			
		
			 //echo $rowData[0][2] ."<BR>";
				$checkIfExsit = ItemPrice::getItemByCompany(array('CompanyCode' =>$old->ErosCode, 'Code' =>$rowData[0][2]  ));
				
				//echo count($checkIfExsit)."<BR>";die();
				//echo $checkIfExsit[0]->Id;
				
				//die();
				if(count($checkIfExsit) ==0 )
				{
					ItemPrice::insertItemPrice(array(
						'Code'		=> $rowData[0][2]
						,'Description'	=> $rowData[0][3]
						,'CompanyCode'	=> $old->ErosCode
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
						,'CompanyCode'	=> $old->ErosCode
						,'Price'		=> $rowData[0][1]
						,'InputDate'	=> date('Y-m-d')	
						,'InputBy'		=> Auth::user()->username
						,'ErosStatus'	=> 'reUpdate'
						,'CebuStatus'	=> 'reUpdate'
					));
					
				}
				
				
		
		}
	}
DB::connection('Eros')->update("UPDATE `Company` SET `reUpload` = 'Done' WHERE `Id` = '".$old->Id."' ");
}
	//DB::connection('Eros')->commit(); 
	
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
	return view('eros.companyLab2Lab', ['datas' => $datas, 'postLink' => url(session('userBU').'/erosui/company/itemspackages/lab2lab/'.$id)]); 
	
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
