<?php

namespace App\Http\Controllers\cms\pages\queue;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Http\Controllers\zennya\api\ZennyaController;

class QueuePageScanTransactionTempController extends Controller
{
    public function iAuth()
    {
	//$url = 'https://dev.api.zennya.com/api/1/admins/login';
	$url = 'https://api.zennya.com/api/1/admins/login';
	$myvars = json_encode(array('email' => 'newworld@zennya.com', 'password' => '6xs6efn80g46nrxg'));
	//$myvars = json_encode(array('email' => 'newworld@zennya.com', 'password' => 'yJdripWHyHgm'));
	$headers = array();
	$headers[] = 'Content-Type: application/json';
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $myvars);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	// receive server response ...
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	$returned = $server_output = curl_exec ($ch);
	curl_close ($ch);
	// end connect 2020
	$eReturned = json_decode($returned);

	return $eReturned->token;
	
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	$companys = DB::connection('Eros')->table('Company')
					->where('Status','Active') // active
					->get(array('Id','Code','Name','Group as Category'));
	$doctors = DB::connection('Eros')->table('Physician')
					->where('Status','Active') // active
					->get(array('Id','FullName','Description as Category'));
	return view('cms/pages.queueScanTempCreateAdd', ['doctors' => $doctors, 'companys' => $companys ] );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
	
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
	//$ZennyaController::iAuth();
	//die();
    //Auth::user()->username
		$user = Auth::user();
		
		// insert 
		if( is_array($request->input('itemSelected')) )
		{
			DB::connection('CMS')->beginTransaction();
			$zennya = DB::connection('Zennya')->table('ProductsDetails')->where('SystemID', $request->input('Id') )->get(array('*'))[0];
		
			$max = DB::connection('Eros')->select("SELECT SUBSTR(MAX(Code),-4) as iMax from Patient where Code like 'Z".date('Ymd')."%' " );
			$x = ($max[0]->iMax != 0)?$max[0]->iMax+1:1;
			
			$gender = (($zennya->gender == 'male')? 'M' : (($zennya->gender == 'female') ? 'F': '') );
			$patientId = DB::connection('Eros')->table('Patient')
				->insertGetId([
					'Code'		=> "Z".date('Ymd').sprintf('%04d', $x++)
					,'FullName'	=> strtoupper(Str::of(htmlentities($zennya->last_name. ", ". $zennya->first_name." ".$zennya->middle_name, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8'))->replaceMatches('/ {2,}/', ' '))
					,'LastName'	=> strtoupper(Str::of(htmlentities($zennya->last_name, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8'))->replaceMatches('/ {2,}/', ' '))
					,'FirstName'	=> strtoupper(Str::of(htmlentities($zennya->first_name, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8'))->replaceMatches('/ {2,}/', ' '))
					,'MiddleName'	=> strtoupper(Str::of(htmlentities($zennya->middle_name, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8'))->replaceMatches('/ {2,}/', ' '))
					,'Prefix'		=> ''
					,'Suffix'		=> ''
					,'Gender'		=> $gender
					,'DOB'		=> $zennya->birthday
					//,'Religion'		=> strtoupper(Str::of(htmlentities($request->input('MiddleName'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8'))->replaceMatches('/ {2,}/', ' '))
					//,'PhilHealth'	=> oci_result($stid, 'PM_PHILHEALTH')
					,'SeniorId'		=>  ''
					
					,'FullAddress'	=> ''
					,'Address'		=> ''
					,'Barangay'	=> ''
					,'City'		=> ''
					,'State'		=>  ''
					//,'ZipCode'	=> oci_result($stid, 'PM_ZIPCODE')	
					,'ContactNo'	=> ''
					//,'FaxNo'		=> oci_result($stid, 'PM_FAXNO')
					,'Email'		=> ''
					,'Moblie'		=> ''
					,'InputDate'	=> date('Y-m-d')
					,'InputBy'		=> Auth::user()->username
					//,'UpdateDate'	=> date('Y-m-d')
					//,'UpdateBy'	=> Auth::user()->username
					,'Status'		=> 'Y'
					,'IsActive'		=> '1'
					,'Country'		=> ''
					,'LastVisit'		=> date('Y-m-d')
					,'PictureLink'	=> ''
					//,'Nationality'	=> htmlentities(oci_result($stid, 'PM_NATIONALITY'), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8')
					,'PassPortNo'	=> ''
					//,'RDOB'		=> oci_result($stid, 'PM_DOB')
					
					
				]);
				
			$dataTime = date('Y-m-d H:i:s');
			$max = DB::connection('CMS')->select("SELECT SUBSTR(MAX(Code),-3) as iMax from Queue where Code like 'Z".date('Ymd')."%' " );
			$xMax = ($max[0]->iMax != 0)?$max[0]->iMax+1:1;
			
			
			$queueId = DB::connection('CMS')->table('Queue')->insertGetId([
				'IdBU'		=> session('userClinicCode'),
				'Code'		=> "Z".date('Ymd').sprintf('%03d', $xMax),
				'Date' 		=> date('Y-m-d'),
				'DateTime' 	=> $dataTime,
				'IdPatient'		=> $patientId,
				'AgePatient'	=> $zennya->age,
				'Notes'		=> 'From Zennya',
				'PatientType'	=> 'Corporate',
				//'AccessionNo'	=> $request->input('accession'),
				'Status'		=> 201,
				'InputBy'		=> Auth::user()->username
			]);	
			
			
			
		
		
		
			$doctor = DB::connection('Eros')->table('Physician')->where('Id', $request->input('DoctorId') )->get(array('Id','FullName'));
			$company = DB::connection('Eros')->table('Company')->where('Id', $request->input('CompanyId') )->get(array('Id','Code','Name'))[0];
			foreach($request->input('itemSelected') as $item)
			{
				$itemPrice = DB::connection('Eros')->table('ItemPrice')->where('Id', $item['Id'] )->get(array('Id', 'Code', 'Description', 'PriceGroup', 'Price'))[0];
				
				DB::connection('CMS')->table('Transactions')->insertGetId([
					'IdQueue'				=> $queueId,
					'Date' 				=> date('Y-m-d'),
					'IdDoctor' 				=> '8498',
					'NameDoctor' 			=> 'OUTSIDE PHYSICIAN',
					'IdCompany'			=> $company->Id,
					'NameCompany'			=> $company->Name,
					'IdItemPrice'			=> $item['Id'],
					'CodeItemPrice'			=> $itemPrice->Code,
					'DescriptionItemPrice'		=> $itemPrice->Description,
					'PriceGroupItemPrice'		=> $itemPrice->PriceGroup,
					'AmountItemPrice'		=> $itemPrice->Price, 
					'InputBy'				=> $user->username,
					'InputId'				=> $user->id,
					'Status'				=> '210'
				]);
			}
		
			// update status accepted
			
			$iToken = $this->iAuth();
			
			
			$url = 'https://api.zennya.com/api/1/medical/specimens/accept';
			$myvars = json_encode(array('product_code' => $zennya->barcodes));
			$headers = array();
			$headers[] = 'Content-Type: application/json';
			$headers[] = "x-auth-token:".$iToken."";
		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $myvars);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			// receive server response ...
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			$returned = $server_output = curl_exec ($ch);
			curl_close ($ch);
			
			$dReturned = json_decode($returned);
		
			
			$getStatus = $this->edit($zennya->barcodes);
			
			DB::connection('Zennya')->table('ProductsDetails')->where('SystemID', $request->input('Id') )->update(['acceptStatus' => $getStatus]);
			DB::connection('CMS')->commit(); 
		
		
		}
		
		
		return $queueId;
		
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
	// scan barcode start here
	$iToken = $this->iAuth();
	//$url = 'https://dev.api.zennya.com/api/1/medical/orders/details?product_code='.$id;
	$url = 'https://api.zennya.com/api/1/medical/orders/details?product_code='.$id;

	$headers = array();
	$headers[] = "x-auth-token:".$iToken."";
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	$returned = $server_output = curl_exec ($ch);
	curl_close ($ch);
	$dReturned  = json_decode($returned);
	//print_r($dReturned);
	//die();
	
	if(isset($dReturned->code) && $dReturned->code == "INVALID_PRODUCT")
	{
		return $dReturned; 
	}
	else
	{
		DB::connection('Zennya')->beginTransaction();
		$dataInsert = 
				[
				   // [
					'id'				=> $dReturned->id
					,'batch_id'			=> $dReturned->batch_id
					,'owner'			=> json_encode($dReturned->owner)
					,'packages'		=> json_encode($dReturned->packages)
					,'tests'			=> json_encode($dReturned->tests)
					,'resources'		=> json_encode($dReturned->resources)
					,'date_created'		=> str_replace("Z", "", (str_replace("T", " ", $dReturned->date_created)))
					,'date_collected'		=> str_replace("Z", "", (str_replace("T", " ", $dReturned->date_collected)))
					,'date_endorsed'	=> str_replace("Z", "", (str_replace("T", " ", $dReturned->date_endorsed)))
					,'date_processed'	=> str_replace("Z", "", (str_replace("T", " ", $dReturned->date_processed)))
					,'barcodes'			=> $dReturned->barcodes
					,'publishable'		=> $dReturned->publishable
					,'total_amount'		=> $dReturned->total_amount
					,'laboratory'		=> json_encode($dReturned->laboratory)
					,'affiliation'		=> $dReturned->affiliation
					,'cif_id'			=> $dReturned->cif_id
					,'order_identifiers'	=> json_encode($dReturned->order_identifiers)
					,'hmo_partner'		=> $dReturned->hmo_partner
					,'loa'				=> $dReturned->loa
					,'expedite'			=> json_encode($dReturned->expedite)
					,'result_status'		=> $dReturned->result_status
					,'result_eval_status'	=> $dReturned->result_eval_status
					,'result_eval'		=> $dReturned->result_eval
					,'medical_provider'	=> json_encode($dReturned->medical_provider)
					,'scan_code'		=> $id
					,'first_name'		=> $dReturned->owner->first_name
					,'middle_name'		=> $dReturned->owner->middle_name
					,'last_name'		=> $dReturned->owner->last_name
					,'gender'			=> $dReturned->owner->gender
					,'birthday'			=> str_replace("Z", "", (str_replace("T", " ", $dReturned->owner->birthday)))
					,'age'			=> $dReturned->owner->age
				    //]
				];
				
		$idScan = DB::connection('Zennya')->table('ProductsDetails')->insertGetId($dataInsert);
	
		DB::connection('Zennya')->commit();
	}
	
	$iSelect =  DB::connection('Zennya')->table('ProductsDetails')->where('SystemID', $idScan )->get(array('*'))[0];
	$return = array();
	$owner = json_decode($iSelect->owner);
	$return['scanId'] = $idScan;
	$return['first_name'] 	= $owner->first_name;
	$return['middle_name'] = $owner->middle_name;
	$return['last_name'] = $owner->last_name;
	$return['gender'	] = $owner->gender;
	$return['birthday'] = str_replace("Z", "", (str_replace("T", " ", $owner->birthday)));
	$return['age'] = $owner->age;
	$return['company_id'] = '2553';
	$Company  = DB::connection('Eros')->table('Company')->where('Id', '2553')->get(array('Code'))[0]; 
	$resources = json_decode($iSelect->resources);
	foreach($resources as $resource)
	{
		if($resource->product_code == $id){ 
			$return['resource_id'] = $resource->resource->id;
			$return['resource_type'] = $resource->resource->type;
			$return['resource_label'] = $resource->resource->label;
			$return['product_id'] = $resource->product_id;
			$return['product_code'] = $resource->product_code;
			$return['collected_date'] = str_replace("Z", "", (str_replace("T", " ", $resource->collected_date)));
			$return['status_name'] = $resource->status->name;
			$return['status_label'] = $resource->status->label;
			$return['tat_max_hours'] = $resource->tat_max_hours;
			$itest  = $resource->tests;
			//print_r($itest);
			//die();
			$return['listItemPrice'] =  DB::connection('Eros')->table('ItemPrice')
				->where('CompanyCode', $Company->Code)
				->Where(function($query) use ($itest) {
				
					foreach($itest as $test)
					{ 
						$query->orWhere('Code',$test->lab_code);
					}
				})
				->get(array('Id as IdItem','Code','Description','Price', 'PriceGroup'))
				->toArray();
				
			
		}
	}
	
	
	return json_encode($return);
	//$eReturned = json_encode($return, JSON_PRETTY_PRINT) ;
	//return "<pre>" . $eReturned . "<pre/>";
	
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
	/*
		$iToken = $this->iAuth();
	
		$url = 'https://api.zennya.com/api/1/medical/specimens/details?product_code='.$id;
		$headers = array();
		$headers[] = "x-auth-token:".$iToken."";
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		$returned = $server_output = curl_exec ($ch);
		curl_close ($ch);
		$dReturned  = json_decode($returned);
		
		
		DB::connection('Zennya')->table('ProductsDetails')->where('SystemID', $id )->update(['acceptStatus' => json_encode($dReturned) ]);
	*/
	
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
	
	
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
