<?php

namespace App\Http\Controllers\cms\doctors;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\cms\Queue;
use App\Models\eros\ErosDB;
use Carbon\Carbon;
class DoctorsCompleteQueueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
	$docIn = DB::connection('Eros')->table('Physician')->where('EmpId', '=', Auth::user()->AccessMapId)->get(array('Id'));
	
	if(count($docIn) == 0)
	{
		
		$doctorsQueue = json_encode(array());
		return view('cms.doctor.doctorsPastQueue', ['doctorsqueue' => $doctorsQueue])->withErrors(['error' => "Missing Physician EmpId Mapping, please contact ICT helpdesk"]);
	}
	
	
	$doctorsQueue = DB::connection('CMS')->table('Queue')
		    ->leftJoin('CMS.AccessionNo', 'CMS.Queue.Id', '=', 'CMS.AccessionNo.IdQueue')
		    ->leftJoin('CMS.VitalSign', 'CMS.Queue.Code', '=', 'CMS.VitalSign.QueueCode')
		    ->leftJoin('CMS.QueueStatus', 'CMS.AccessionNo.Status', '=', 'CMS.QueueStatus.Id')
		    ->where('CMS.Queue.date', date('Y-m-d'))
		    ->where('CMS.AccessionNo.ItemGroup', 'CLINIC')
		    ->whereIn('CMS.AccessionNo.ItemSubGroup', [ 'INDUSTRIAL', 'CONSULTATION' ] )
		    ->where('CMS.VitalSign.PcpId', '=', $docIn[0]->Id)
		    ->whereIn('CMS.AccessionNo.Status', ['500'])
		    //->groupby('CMS.Queue.Code')
		    ->get(['CMS.Queue.Code as QCode', 'CMS.Queue.QFullName', 'CMS.Queue.Date', 'CMS.QueueStatus.Name as QueueStatus', 'CMS.Queue.InputBy', 'CMS.Queue.Id', 'CMS.AccessionNo.ItemDescription', 'CMS.AccessionNo.ItemCode as AItemCode']);
	 return view('cms.doctor.doctorsPastQueue', ['doctorsqueue' => $doctorsQueue]);
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return $id;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
	$docIn = DB::connection('Eros')->table('Physician')->where('EmpId', '=', Auth::user()->AccessMapId)->get(array('Id'));
	
	if(count($docIn) == 0)
	{
		return "Missing Physician Mapping";
		die();
	}
	
	$Que = DB::connection('CMS')->table('Queue')->where('Id', $id)->first();

	$vital = DB::connection('CMS')->table('VitalSign')->where('QueueCode', 'LIKE', $Que->Code)->first();
        
	$queue = DB::connection('CMS')->table('Queue')
                ->leftJoin('CMS.Transactions', 'CMS.Queue.Id', '=', 'CMS.Transactions.IdQueue')    
                ->leftJoin('CMS.AccessionNo', 'CMS.Queue.Id', '=', 'CMS.AccessionNo.IdQueue')
                ->leftJoin('CMS.QueueStatus', 'CMS.AccessionNo.Status', '=', 'CMS.QueueStatus.Id')
                ->leftJoin('Eros.Physician', 'CMS.Transactions.IdDoctor', '=', 'Eros.Physician.Id')
                ->leftJoin('Eros.Patient', 'CMS.Queue.IdPatient', 'Eros.Patient.Id')
               // ->where('CMS.Queue.date', date('Y-m-d'))
                ->where('CMS.Queue.Id', $id)
                ->whereIn('CMS.AccessionNo.Status', ['500'])
		->where('CMS.AccessionNo.ItemGroup', 'CLINIC')
		->whereIn('CMS.AccessionNo.ItemSubGroup', [ 'INDUSTRIAL', 'CONSULTATION' ] )
		->where('CMS.AccessionNo.IdDoctor', $docIn[0]->Id)
                ->get(['Queue.IdBU as IdClinic', 'Queue.IdBU','Transactions.NameCompany','CMS.Queue.Code as QCode', 'Eros.Patient.FullName', 'Eros.Patient.PictureLink','CMS.Queue.Date', 'CMS.QueueStatus.Name as QueueStatus', 'CMS.Queue.InputBy', 'CMS.Queue.Id', 'CMS.Transactions.TransactionType', 'CMS.Transactions.IdItemPrice', 'CMS.Transactions.PriceGroupItemPrice', 'CMS.AccessionNo.ItemDescription', 'Eros.Patient.Code as PatientCode', 'CMS.Queue.IdPatient', 'Eros.Patient.DOB','Eros.Patient.Gender','CMS.Queue.AgePatient','CMS.Queue.DateTime' ,'Queue.Notes','Eros.Physician.Id as IdDoctor'])[0];


        $soapInfo = DB::connection('CMS')->table('SOAP')->where('QueueCode', 'LIKE' , $Que->Code)->get(['Subjective', 'Objective', 'Assessment', 'Plan']);
        if ($soapInfo->isNotEmpty()) {
            $soapInfo = $soapInfo[0];
        } else {
            $soapInfo = DB::connection('CMS')->table('SOAPTemp')->where('QueueCode', 'LIKE', $Que->Code)->get(['Subjective', 'Objective', 'Assessment', 'Plan'])[0] ?? '';
        }
        $transactionType = DB::connection('CMS')->table('Transactions')
		->leftJoin('Eros.ItemMaster','Transactions.CodeItemPrice','=','Eros.ItemMaster.Code')
		->leftJoin('CMS.AccessionNo', 'Transactions.IdQueue', '=', 'CMS.AccessionNo.IdQueue')
		->where('Transactions.IdQueue', $id)
		->where('CMS.AccessionNo.ItemCode', 'LIKE', 'CN001') 
		->get(['TransactionType','Transactions.NameCompany','Transactions.Id', 'Transactions.PriceGroupItemPrice', 'CMS.AccessionNo.ItemCode as ItemCode']);

	$PastConsultation = DB::connection('CMS')->table('SOAP')
		    ->Join('Queue','Queue.Code','=','SOAP.QueueCode')
                    ->leftJoin('QueueStatus','SOAP.Status','=','QueueStatus.Id')
                    ->where('CMS.SOAP.IdPatient', '=' ,$Que->IdPatient)
                    //->whereDate('CMS.Queue.Date', '<>', Carbon::today())
		    ->where('CMS.Queue.Status', '>=', 210)
		    ->where('CMS.Queue.Status', '<=', 600)
                    ->groupBy('CMS.SOAP.QueueCode', 'CMS.SOAP.IdPatient', 'CMS.SOAP.IdDoctor')
                    ->get(['CMS.SOAP.Id','CMS.Queue.Id as QueueId','CMS.Queue.Code as QCode', 'CMS.SOAP.SystemDateTime as DateTime' ,'CMS.SOAP.NameDoctor', 'CMS.SOAP.Subjective','CMS.SOAP.Objective','CMS.SOAP.Assessment','CMS.SOAP.Plan', 'CMS.SOAP.Id as SOAPId']);
                    
	$Result = DB::connection('CMS')->table('Queue')
                        ->Join('AccessionNo', 'Queue.Code', 'LIKE', 'AccessionNo.QueueCode')
			->leftJoin('QueueStatus', 'AccessionNo.Status', '=', 'QueueStatus.Id')
                        ->where('Queue.IdPatient', '=', $Que->IdPatient)
                        ->where('AccessionNo.ItemGroup', 'NOT LIKE', 'CLINIC')
                        ->where('Queue.Status','>=', 210)
                        ->where('Queue.Status', '<=', 600)
			->where('Queue.Status', '!=', 900)
                        ->get(['AccessionNo.Id as AccessionId','Queue.Date as Date', 'AccessionNo.ItemCode', 'AccessionNo.ItemDescription' ,'AccessionNo.Status', 'QueueStatus.Name as ItemStatus', 'Queue.InputBy', 'Queue.Id as QueueId', 'AccessionNo.IdTransaction as IdTransaction']);
        //dd($Result);
       
        $PEdata = DB::connection('CMS')->table('PhysicalExaminationReport')->where('IdQueue', $id)->get();
        // dd($PEdata)
        $GP = $PEdata[0]->GP ?? '';
        preg_match('/(\d)(\d)\((\d+-\d+-\d+-\d+)\)/', $GP, $matches);

        // Assign the values
        $g_value = $matches[1] ?? '';
        $p_value = $matches[2] ?? '';
        $p1_value = $matches[3]?? '';
        //dd($p1_value);
        $packageIds = DB::connection('CMS')->table('Transactions')
                    ->leftJoin('Eros.Package', 'Transactions.IdItemPrice', '=', 'Eros.Package.ItemPriceId')
                    ->leftJoin('CMS.AccessionNo', 'CMS.Transactions.IdQueue', '=', 'CMS.AccessionNo.IdQueue')
                    ->leftJoin('CMS.QueueStatus', 'CMS.AccessionNo.Status', '=', 'CMS.QueueStatus.Id')
                    ->where('CMS.Transactions.date', date('Y-m-d'))
                    ->where('CMS.Transactions.IdQueue', $id)
                    ->whereIn('CMS.AccessionNo.Status', ['280', '500'])
                    ->where('Eros.Package.ItemCode', 'LIKE', 'CI002') // MEDICAL AND PHYSICAL EXAMINATION
                    ->get(['CMS.Transactions.IdQueue']);
                    //->pluck('CMS.Transactions.IdQueue');
       
       
               //  dd($packageIds);
        return view('cms.doctor.doctorsPastQueueEdit', [ 'datas' => $queue, 'g_value' => $g_value, 'p_value' => $p_value, 'p1_value' => $p1_value,'PEdata' => $PEdata, 'vitals' => $vital ,'postLink'=>'', 'soap' => $soapInfo, 'pastConsultation' => $PastConsultation , 'transactionType' => $transactionType, 'Results' => json_encode($Result), 'package' => $packageIds]);
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
	DB::connection('CMS')->beginTransaction();
		$docIn = DB::connection('Eros')->table('Physician')->where('EmpId', '=', Auth::user()->AccessMapId)->get(array('Id'));
		
		if(count($docIn) == 0)
		{
			return "Missing Physician Mapping";
			die();
		}
		DB::connection('CMS')->table('AccessionNo') ->whereIn('CMS.AccessionNo.Status', ['500'])
		->where('CMS.AccessionNo.ItemGroup', 'CLINIC')
		->whereIn('CMS.AccessionNo.ItemSubGroup', [ 'INDUSTRIAL', 'CONSULTATION' ] )
		->where('CMS.AccessionNo.IdQueue', $id)->where('CMS.AccessionNo.IdDoctor', '=', $docIn[0]->Id)->update(['Status' => 280]);
           
	DB::connection('CMS')->commit(); 
        return $id;      
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function getAjax()
    {
        return 'Testing History';
    }
}
