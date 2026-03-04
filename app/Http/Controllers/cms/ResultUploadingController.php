<?php

namespace App\Http\Controllers\cms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use App\Models\eros\ErosDB;

class ResultUploadingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {	
	  //return view('cms.ErosPatientListServer');
       
	
	//$queue = Queue::todaysQueue()->get(array('CMS.Queue.Id','CMS.Queue.Code','Eros.Patient.FullName','CMS.QueueStatus.Name as QueueStatus','CMS.Queue.InputBy','CMS.Queue.Notes'));
	//return view('cms.queue', ['queue' => $queue]);
	
	
/*	
$msg = urlencode(
'Good day, 

Greetings from New World Diagnostics. 
Thank you for reaching out to us. 
This is Rica from CSR Dept.

How can  we help you?'
);
	$smsno = '09610060876';
	$url =  "http://10.30.40.3/cgi/WebCGI?1500101=account=ictapi&password=1Ctd@2022&port=1&destination=".$smsno.'&content='.$msg;
	
	
	$headers = array();
	$headers[] = "Content-Type: application/json;charset=utf-8";
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPGET, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	// receive server response ...
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	$returned = $server_output = curl_exec ($ch);
	
	if ((strpos($server_output, 'Response: Success') !== false) && ((strpos($server_output, 'Message: Commit successfully!') !== false)))
	{
		echo $SMS_success = 'YES';
	}
	else
	{
		echo $SMS_success = 'NO';
	}
	curl_close ($ch);
*/	
	


	
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
      
    }
}
