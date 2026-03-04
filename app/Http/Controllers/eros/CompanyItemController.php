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

class CompanyItemController extends Controller
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
		return 'ricky';//view('eros.physicianListCreate');
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
	//$packageData = ErosDB::getCompanyPackageData($id);
	$itemData = ErosDB::getItemData(NULL,$datas[0]->ErosCode);
	$clinics = ErosDB::getClinicData();
	return view('eros.companyItemNew', ['itemData'=>json_encode($itemData), 'clinics'=> $clinics, 'datas' => $datas, 'postLink' => url(session('userBU').'/erosui/company/itemspackages/item/'.$id)]); 
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
    echo 'edit';die();
	//$datas = ErosDB::getCompanyData($id);
	//$itemPrice = ErosDB::getItemPriceData($datas[0]->Code);
	//return view('eros.companyItem', ['itemPrice'=>json_encode($itemPrice), 'datas' => $datas, 'postLink' => url(session('userBU').'/erosui/company/item/'.$id)]); 
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
    
    public function newSaveAjax()
    { 
	if( ! $checking = $this->CheckModuleAccess() )
	{
		return response(['error' => ['code' => 'INSUFFICIENT ROLE for ',	'description' => 'You are not authorized to access this resource.']], 401);
	}
	DB::connection('Eros')->beginTransaction();
	try {
		$checkItemPriceIfExist = ErosDB::getItemData($_POST['itemCode'],$_POST['companyCode']);
		if( count($checkItemPriceIfExist) != 0) // update Item Price
		{ //ErosStatus
			
			DB::connection('Eros')->update("UPDATE ItemPrice SET 
			Price = '".$_POST['Price']."' ,
			UpdateDate = '".date('Y-m-d')."',
			UpdateBy = '".Auth::user()->username."',
			ErosStatus = 'reUpdate',
			CebuStatus = 'reUpdate',
			SMBStatus = 'reUpdate'
			WHERE Id = '".$checkItemPriceIfExist[0]->Id."' ");
			
		}
		else
		{
			$data = 
			[
			    [
				'ClinicCode'		=> "ALL",
				'Code'			=> $_POST['itemCode'],
				'Description'		=> $_POST['itemDescription'],
				'DescriptionEros'	=> $_POST['itemDescription'],
				'CompanyCode'		=> $_POST['companyCode'],
				'Price'			=> $_POST['Price'],
				'PriceGroup'		=> $_POST['itemType'],
				'InputDate'		=> date('Y-m-d'),
				'InputBy'			=> Auth::user()->username,
				'ErosStatus'		=> 'reUpdate',
				'CebuStatus'		=> 'reUpdate',
				'SMBStatus' 		=> 'reUpdate'
				
			    ]
			];
			
			DB::connection('Eros')->table('ItemPrice')->insert($data);
		}
		DB::connection('Eros')->commit();
	} catch (\Exception $e) {
		DB::connection('Eros')->rollback();
		return $e;
	}
	return "Okay";
    }
    
    
}
