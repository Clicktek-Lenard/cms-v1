<?php

namespace App\Http\Controllers\zennya\api;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ZennyaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		//$queue = Queue::todaysQueue()->get();
		return view('zennya.queue', ['queue' => '']);
    
    }
     
    public function iAuth()
    {
    
	//prod
	 $url = 'https://api.zennya.com/api/1/admins/login';
	 $myvars = json_encode(array('email' => 'newworld@zennya.com', 'password' => '6xs6efn80g46nrxg'));
    
	//$url = 'https://dev.api.zennya.com/api/1/admins/login';
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $labNo = '2300244977';
	
	
	$cmsLab = DB::connection('CMS')->table('Queue')
		->leftJoin('Transactions', 'Queue.Id', '=', 'Transactions.IdQueue')
		->whereNotNull('Queue.Lab2LabId')
		->where('Transactions.Status', 500)
		->where('Transactions.CodeItemPrice', 'LIKE', 'ALT' )
		->get(array('Transactions.*','Queue.LabId', 'Queue.LabBarcode', 'Queue.Lab2LabId'));
	
	foreach($cmsLab as $cms)
	{
		$HData =  DB::connection('oraPRODh')
					->table('ORD_HDR tb1')
					->leftJoin('ORD_DTL tb2', 'tb1.OH_TNO' ,'=', 'tb2.OD_TNO')
					->leftJoin('TEST_ITEM tb3', 'tb2.OD_TESTCODE' ,'=', 'tb3.TI_CODE') 
					->leftJoin('USER_ACCOUNT tb4', 'tb2.OD_VALIDATE_BY', '=', 'tb4.USER_ID')
					->where('tb1.OH_TNO', $cms->LabId)
					->where('tb2.OD_ORDER_TI', $cms->CodeItemPrice)
					->orderBy('tb2.OD_SEQ_NO', 'ASC')
					->get(array(
						'tb1.oh_last_name', 'tb1.oh_bod', 'tb1.oh_sex', 'tb1.oh_trx_dt',
						'tb2.od_test_grp', 'tb3.ti_name', 'tb2.od_testcode', 'tb2.od_tr_val', 'tb2.od_tr_unit' ,  'tb2.od_mrr_desc', 'tb2.od_tr_range', 'tb2.od_tr_flag',
						'tb4.user_name', 'tb4.user_ldap_id'
					));
		
		
		$studies = array();
		
		foreach($HData as $hc)
		{
			array_push($studies, array
				(
					"Category"		=> $hc->od_test_grp
					,"name"		=> $hc->ti_name
					,"code"		=> $hc->od_testcode
					,"flag"		=> $hc->od_tr_flag
					,"si_result"	=> $hc->od_tr_val
					,"si_unit"		=> $hc->od_tr_unit
					,"si_refrange"	=> $hc->od_tr_range
					,"conv_result"	=> ""
					,"conv_unit"	=> ""
					,"conv_refrange"=> ""   // $hc->od_mrr_desc
					,"comments"	=> ""
				)
			);
		
		}

	
		$results = 
		array
		(
			array
			(
				"firstname" 		=> $HData[0]->oh_last_name
				,"middlename" 		=> ""
				,"lastname"		=> ""
				,"birthdate"		=> $HData[0]->oh_bod
				,"gender"			=> ($HData[0]->oh_sex == '1') ? 'MALE' : 'FEMALE'
				,"registration_date"	=> $HData[0]->oh_trx_dt
				,"referring_doctor"	=> "-"
				,"important_notice"	=> ""
				,"test_status"		=> "RELEASED"
				,"lab_id"			=> $cms->LabBarcode
				,"ref_id"			=> $cms->Lab2LabId
				,"studies"			=> $studies 
									
				,"scientist"	 		=> array
									(
										"firstname"	=> $HData[0]->user_name
										,"lastname"	=> ""
										,"title"		=> ""
										,"license"		=> "RMT Lic. No. ".$HData[0]->user_ldap_id
									)
				,"pathologist"		=> array
									(
										"firstname"	=> "DR. PAULO GIOVANNI"
										,"middlename"	=> "L"
										,"lastname"	=> "MENDOZA"
										,"title"		=> ""
										,"license"		=> "FPSP Lic. No. 98131"
									)
			)
		);
		
		$iToken = $this->iAuth();
		$url = 'https://api.zennya.com/api/1/medical/results/json';
		$myvars = json_encode($results);
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
		
		//$eReturned = json_decode($returned);
			
		echo "<pre>";
		print_r($results);
		echo "</pre>";
		
	
	}
	
		
		
	
	die('Done');
	
	
		
	
	/*
	
	*/
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
	
	
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
   
	//uploading PDF  FILE
	//01000LWY4DKK_900405.pdf
	//01200M4Z7X4C_900405.pdf

	$iToken = $this->iAuth();
	$url = 'https://api.zennya.com/api/1/medical/results';
	$headers = array();
	$headers[] = "x-auth-token:".$iToken."";
	$headers[] =  "Content-Type: multipart/form-data";
	
	$fileName = '01200M4Z7X4C_900405.pdf';
	
	$ch = curl_init();
	$file = curl_file_create(public_path().'/zennya/'.$fileName);
	$myvars = json_encode(array('filename' => $fileName, 'name' => $file));
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $myvars); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	// receive server response ...
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	$returned = $server_output = curl_exec ($ch);
	curl_close ($ch);
	
	echo "<pre>";
	print_r($returned);
	echo "</pre>";
	die();
	
	
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
	$iToken = $this->iAuth();
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
	
	print($returned);
	die();
	//$dReturned  = json_decode($returned);
	/*
	DB::connection('Zennya')->beginTransaction();
		$dataInsert = 
				[
				    [
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
				    ]
				];
				
				DB::connection('Zennya')->table('ProductsDetails')->insert($dataInsert);
	
	DB::connection('Zennya')->commit();*/
	
	$eReturned = json_encode($dReturned, JSON_PRETTY_PRINT) ;
	return "<pre>" . $eReturned . "<pre/>";
	
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
        //
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
