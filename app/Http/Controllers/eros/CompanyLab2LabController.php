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
            'file' => 'required|mimes:xlsx|max:8048',
        ]);

        $fileName = time().'.'.$request->file->extension();  
        $request->file->move(public_path('uploads/Physician'), $fileName);

	
	//DB::connection('Eros')->beginTransaction();
	$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('uploads/Physician')."/".$fileName);
	//  Get worksheet dimensions
	$sheet = $spreadsheet->getActiveSheet(); 
	$highestRow = $sheet->getHighestRow(); 
	$highestColumn = $sheet->getHighestColumn();
	//  Loop through each row of the worksheet in turn
	for ($row = 2; $row <= $highestRow; $row++)
	{ 
		//  Read a row of data into an array
		$rowData = $sheet->rangeToArray('A' . $row . ':T' . $row,NULL,TRUE,FALSE);
		if(!empty($rowData[0][19]) )
		{
			$lastname  =  ($rowData[0][16] == 'Inactive') ? "(INACTIVE) ".$rowData[0][3]:$rowData[0][3];
			DB::connection('Eros')->table('Physician')
			->updateOrInsert(
				['ErosCode' => trim($rowData[0][19])],
				[
					'Description'		=> $rowData[0][9]
					,'SubDescription'	=> $rowData[0][10]
					,'XFullName'		=> $rowData[0][2]
					,'XErosCode'		=> $rowData[0][19]
					,'XId'				=> $rowData[0][0]
					,'Status'			=> $rowData[0][16]
					,'LastName'		=> $lastname
					,'FirstName'		=> $rowData[0][4]
					,'MiddleName'		=> $rowData[0][5]
					,'SECode'			=> $rowData[0][13]
					,'DisplayName'		=> $rowData[0][12]
					,'FullName'		=> $rowData[0][12]
					,'Suffix'			=> $rowData[0][6]
				]
			); 
		}
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
