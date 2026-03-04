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

class VitalsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	public function index()
	{
		$lessDate=Date('Y-m-d', strtotime('-3 days'));
		$docIn = DB::connection('Eros')->table('Physician')->where('EmpId', '=', Auth::user()->AccessMapId)->get(array('Id'));
		
		$vitalSign = DB::connection('CMS')->table('Queue')  
		->Join('CMS.AccessionNo', 'CMS.Queue.Id', '=', 'CMS.AccessionNo.IdQueue')
		->Join('CMS.QueueStatus', 'CMS.AccessionNo.Status', '=', 'CMS.QueueStatus.Id')
		->where('CMS.Queue.date', '<=', date('Y-m-d'))
		->where('CMS.Queue.date', '>=', $lessDate)
		//->where('CMS.AccessionNo.Type', 'CLINIC')
		//->where('CMS.AccessionNo.ItemCode', 'LIKE', 'CN001')
		->where('CMS.AccessionNo.Status', '230')
		->where('CMS.Queue.Status', '>=', 210)
		->where('CMS.Queue.Status', '<=', 600)
		->where('CMS.Queue.IdBU', 'LIKE', session('userClinicCode'))
		->where('CMS.AccessionNo.ItemGroup', 'CLINIC')
		->whereIn('CMS.AccessionNo.ItemSubGroup', [ 'INDUSTRIAL','CONSULTATION'])
		->where('IdDoctor', '=',  $docIn[0]->Id)
		->groupBy('CMS.Queue.Id')
		->get(['CMS.Queue.Code as QCode', 'CMS.Queue.QFullName', 'CMS.Queue.Date', 'CMS.QueueStatus.Name as QueueStatus', 'CMS.Queue.InputBy', 'CMS.Queue.Id']);
		// dd($vitalSign);
		return view('cms.doctor.vitalSign', ['vitalSign' => $vitalSign]);
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    { 
        $lessDate=Date('Y-m-d', strtotime('-3 days'));
        $clinics = ErosDB::getClinicData();
        $vital = DB::connection('CMS')->table('VitalSign')->where('IdQueue', $id)->first();
        $Physician = DB::connection('Eros')->table('Physician')->where('Status', 'Approved')->where('SubGroup', 'PCP')->Orwhere('SubGroup', 'SPL')->get(['Code', 'FullName' , 'Description', 'Id']);
     //  dd($Physician);
        $queue = DB::connection('CMS')->table('Queue')
                ->leftJoin('CMS.Transactions', 'CMS.Queue.Id', '=', 'CMS.Transactions.IdQueue')    
                ->leftJoin('CMS.AccessionNo', 'CMS.Queue.Id', '=', 'CMS.AccessionNo.IdQueue')
                ->leftJoin('CMS.QueueStatus', 'CMS.AccessionNo.Status', '=', 'CMS.QueueStatus.Id')
                ->leftJoin('Eros.Physician', 'CMS.Transactions.IdDoctor', '=', 'Eros.Physician.Id')
                ->leftJoin('Eros.Patient', 'CMS.Queue.IdPatient', 'Eros.Patient.Id')
		->leftJoin('Eros.ItemMaster', 'CMS.AccessionNo.ItemCode', 'Eros.ItemMaster.Code')
                //->where('CMS.AccessionNo.Type', 'CLINIC')
                ->where('CMS.Queue.date', '<=', date('Y-m-d'))
		->where('CMS.Queue.date', '>=', $lessDate)
		//->where('CMS.AccessionNo.ItemCode', 'LIKE', 'CN001')
		->where('CMS.AccessionNo.Status', '=' ,230)
		->where('CMS.Queue.Status', '>=', 210)
		->where('CMS.Queue.Status', '<=', 600)
		->where('CMS.Queue.IdBU', 'LIKE', session('userClinicCode'))
                ->where('CMS.Queue.Id', $id)
		->where('CMS.AccessionNo.ItemGroup', 'LIKE', 'CLINIC')
		->whereIn('CMS.AccessionNo.ItemSubGroup', [ 'INDUSTRIAL','CONSULTATION'])
		
                ->get(['CMS.Queue.Code', 'Eros.Patient.FullName', 'Transactions.NameDoctor', 'Transactions.IdDoctor','CMS.Queue.Date', 'CMS.QueueStatus.Name as QueueStatus', 'CMS.Queue.InputBy', 'CMS.Queue.Id', 'CMS.Transactions.TransactionType', 'CMS.Transactions.IdItemPrice', 'CMS.Transactions.PriceGroupItemPrice', 'CMS.AccessionNo.ItemDescription', 'Eros.Patient.Code as PatientCode', 'CMS.Queue.IdPatient', 'Eros.Patient.DOB','Eros.Patient.Gender','CMS.Queue.AgePatient','CMS.Queue.DateTime','Queue.Notes','Queue.IdBU as IdClinic','Queue.IdBU', 'Queue.PatientType'])[0];
     //  dd($queue);
                $Transactions = DB::connection('CMS')->table('Transactions')->where('IdQueue', $id)->where('CodeItemPrice', 'like', '%CN%')->get(['CodeItemPrice']);
        return view('cms.doctor.vitalSignEdit',[ 'datas' => $queue, 'clinics' => $clinics, 'vitals' => $vital , 'Transactions' => $Transactions, 'postLink' => url('/doctor/vitals/'.$id), 'Physician' => $Physician ]);
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
	DB::connection('CMS')->beginTransaction();
	$Que = DB::connection('CMS')->table('Queue')->where('Id', '=', $id)->first();
        DB::connection('CMS')->table('VitalSign')->updateOrInsert(
		[ 'IdQueue'          	=>      $id,
                  'QueueCode'          	=>      $Que->Code
		],
		['ChiefComplaint'   	=>      $request->input('ChiefComplaint')
		,'PcpId'            		=>      $request->input('PcpId')
		,'PcpName'          	=>      $request->input('PcpName')
		,'PulseRate'        	=>      $request->input('PulseRate')
		,'RespiratoryRate'  	=>      $request->input('Respiraroty')
		,'BloodPresure'     	=>      $request->input('BloodPresure')
		,'BloodPresureOver' 	=>      $request->input('BloodPresureOver')
		,'BloodPresure2'   	=>      $request->input('BloodPresure2')
		,'BloodPresureOver2'	=>      $request->input('BloodPresureOver2')
		,'BloodPresure3'    	=>      $request->input('BloodPresure3')
		,'BloodPresureOver3'	=>      $request->input('BloodPresureOver3')
		,'Temperature'      	=>      $request->input('Temperature')
		,'Height'           		=>      $request->input('Height')
		,'Weight'           		=>      $request->input('Weight')
		,'BMI'              		=>      $request->input('BMI')
		,'BMICategory'      	=>      $request->input('BMIcategory')
		,'UcorrectedOD'     	=>      $request->input('unCorOD')
		,'UcorrectedOS'     	=>      $request->input('unCorOS')
		,'CorrectedOD'      	=>      $request->input('CorOD')
		,'CorrectedOS'      	=>      $request->input('CorOS')
		,'UncorrectedNearOD'	=>      $request->input('UncorrectedNearOD')
		,'UncorrectedNearOS'	=>      $request->input('UncorrectedNearOS')
		,'CorrectedNearOD'  	=>      $request->input('CorrectedNearOD')
		,'CorrectedNearOS'  	=>      $request->input('CorrectedNearOS')
		,'Deficient'        		=>      $request->input('Deficient')
		,'ColorVision'      	=>      $request->input('ColorVision')
		,'WithContactLens'  	=>      $request->input('WithLense')
		,'WithEyeGlass'     	=>      $request->input('WithEyeglass')
		,'InputBy'          	=>      Auth::user()->username
	]);
	DB::connection('CMS')->table('Transactions')
		->leftJoin('AccessionNo', 'Transactions.IdQueue', '=', 'AccessionNo.IdQueue')
		->where('AccessionNo.IdQueue', $id)
		->where('AccessionNo.ItemGroup', 'LIKE', 'CLINIC')
		->whereIn('AccessionNo.ItemSubGroup', [ 'INDUSTRIAL','CONSULTATION'])
		->where('Transactions.Status', '230')->update([
			'Transactions.Status'            =>          '280'
	]);
	DB::connection('CMS')->table('AccessionNo')
		->where('IdQueue', $id)
		->where('AccessionNo.Status', '=', '230')
		->where('AccessionNo.ItemGroup', 'LIKE', 'CLINIC')
		->whereIn('AccessionNo.ItemSubGroup', [ 'INDUSTRIAL','CONSULTATION'])
		->update(['Status' => '280']);
	DB::connection('CMS')->commit(); 
       		return $id;
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
