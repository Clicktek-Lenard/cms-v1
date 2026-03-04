<?php

namespace App\Http\Controllers\cms\doctors;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\eros\ErosDB;
use Carbon\Carbon;
use DataTables;

class MedicalEvaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

        /*$queue = DB::connection('CMS')->table('Queue')
                            ->leftJoin('CMS.Transactions', 'CMS.Queue.Id', '=', 'CMS.Transactions.IdQueue')    
                            ->leftJoin('CMS.AccessionNo', 'CMS.Queue.Id', '=', 'CMS.AccessionNo.IdQueue')
                            ->leftJoin('CMS.QueueStatus', 'CMS.Queue.Status', '=', 'CMS.QueueStatus.Id')
                            ->leftJoin('Eros.Physician', 'CMS.Transactions.IdDoctor', '=', 'Eros.Physician.Id')
                            ->leftJoin('Eros.Patient', 'CMS.Queue.IdPatient', 'Eros.Patient.Id')
                            ->leftJoin('CMS.PhysicalExaminationReport', 'CMS.Queue.Id', '=' , 'CMS.PhysicalExaminationReport.IdQueue')
                            ->where('CMS.Queue.IdBU', session('userClinicCode'))
                            //->where('CMS.Queue.date', date('Y-m-d'))
                            //->where('CMS.PhysicalExaminationReport.IdQueue', '!=', 'null')
                            ->where('CMS.Queue.Status', '>=', '300' )
                            ->where('CMS.Queue.Status', '!=', '900' )
                            ->where('CMS.AccessionNo.ItemCode', 'CI002')
                            ->groupBy('CMS.Queue.Id')
                            ->get(['CMS.Queue.Code', 'Eros.Patient.FullName', 'CMS.Queue.Date', 'CMS.QueueStatus.Name as QueueStatus', 'CMS.Queue.InputBy', 'CMS.Queue.Id', 'CMS.Transactions.TransactionType', 'CMS.Transactions.IdItemPrice', 'CMS.Transactions.PriceGroupItemPrice', 'CMS.AccessionNo.ItemDescription']);
                        
        return view('cms.doctor.medicalEval', ['datas' => $queue]);
	*/
	// return route('erosserver.PatientList');
	$lCompa = DB::connection('Eros')->table('Company')->where('ResultUploading', 'LIKE', 'Yes')->get(array('Id', 'ErosCode', 'Name'));
	
	$sid = (isset($_GET['sid']))? $_GET['sid'] : 'ALL';
	
       return view('cms.doctor.medicalEval',  ['fCompany' =>$lCompa, 'sid' => $sid  ]);
	
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
        dd($_GET['ItemCode']);
	$Que = DB::connection('CMS')->table('Queue')->where('Id', $id)->first();
        $clinics = ErosDB::getClinicData();
	$itemVitals = DB::connection('CMS')->table('PEAssesAndRec')->where('QueueCode', $Que->Code)->where('ItemCode', 'LIKE', 'VITALS')->get(['Assessment', 'Recommendation', 'PEAssesAndRec.ItemCode',  'PEAssesAndRec.Class']);
        $AssesAndRec = DB::connection('CMS')->table('PEAssesAndRec')
                        ->where('QueueCode', $Que->Code)
                        ->get(['Assessment', 'Recommendation', 'CMS.PEAssesAndRec.ItemCode',  'CMS.PEAssesAndRec.Class']);
       //dd($AssesAndRec);       
        $PhysicalExaminationReport = DB::connection('CMS')->table('PhysicalExaminationReport')->where('QueueCode', $Que->Code)->get('Class');
        $VitalSigns = DB::connection('CMS')->table('VitalSign')->where('QueueCode', $Que->Code)->get('IdQueue');
        //dd($VitalSigns);
        $queue = DB::connection('CMS')->table('Queue')
                ->leftJoin('CMS.Transactions', 'CMS.Queue.Id', '=', 'CMS.Transactions.IdQueue')    
                ->leftJoin('CMS.AccessionNo', 'CMS.Queue.Id', '=', 'CMS.AccessionNo.IdQueue')
                ->leftJoin('CMS.QueueStatus', 'CMS.AccessionNo.Status', '=', 'CMS.QueueStatus.Id')
                ->leftJoin('Eros.Physician', 'CMS.Transactions.IdDoctor', '=', 'Eros.Physician.Id')
                ->leftJoin('Eros.Patient', 'CMS.Queue.IdPatient', 'Eros.Patient.Id')
                //->where('CMS.Queue.date', date('Y-m-d'))
                ->where('CMS.Queue.Id', $id)
                ->get(['CMS.Queue.Code', 'Eros.Patient.FullName', 'CMS.Queue.Date', 'CMS.QueueStatus.Name as QueueStatus', 'CMS.Queue.InputBy', 'CMS.Queue.Id', 'CMS.Transactions.TransactionType', 'CMS.Transactions.IdItemPrice', 'CMS.Transactions.PriceGroupItemPrice', 'CMS.AccessionNo.ItemDescription', 'Eros.Patient.Code as PatientCode', 'CMS.Queue.IdPatient', 'Eros.Patient.DOB','Eros.Patient.Gender','CMS.Queue.AgePatient','CMS.Queue.DateTime', 'Queue.PatientType','Queue.IdBU as IdClinic','Queue.IdBU','Queue.Notes'])[0];

        $transactionType = DB::connection('CMS')->table('Transactions')
                    ->leftJoin('Eros.ItemMaster','Transactions.CodeItemPrice','=','Eros.ItemMaster.Code')
                    ->where('IdQueue', $id)
                // ->where('Eros.ItemMaster.Group', 'CLINIC') 
                    ->get(['TransactionType','NameCompany','Transactions.Id', 'Transactions.PriceGroupItemPrice', 'Eros.ItemMaster.StandardPackage']);

        $transactionData = DB::connection('CMS')->table('AccessionNo')->where('QueueCode', $Que->Code)
                    ->leftJoin('QueueStatus', 'AccessionNo.Status', '=', 'QueueStatus.Id')
                    ->where('ItemGroup', '!=', 'CLINIC')
                    ->get(['AccessionNo.IdQueue','AccessionNo.QueueCode','ItemDescription', 'IdTransaction', 'ItemCode', 'AccessionNo.Status as AStatus','QueueStatus.Name as Status']);
		    
	foreach($transactionData as $withWaived)
	{
		if($withWaived->AStatus == "888")
		{
			DB::connection('CMS')->table('PEAssesAndRec')->updateOrInsert(
				[
					'QueueCode'	=> $withWaived->QueueCode,
					'ItemCode'		=> $withWaived->ItemCode
				],
				[
					'IdQueue'		=> $withWaived->IdQueue
					,'Findings'		=> '{"Findings1":""}'
					,'Assessment'	=> '{"Assessment1":"WAIVED"}'
					,'Recommendation'=> '{"Recommendation1":null}'
					,'Class'		=> '{"Class1":"WAIVED"}'
					,'InputBy'		=> 'CMS-ADMIN'
				]
			);
		}
	}

        $selectedClasses = (!empty($PhysicalExaminationReport) && isset($PhysicalExaminationReport[0]->Class)) 
        ? explode(', ', $PhysicalExaminationReport[0]->Class) 
        : [];
;
        return view('cms.doctor.medicalEvalEdit', [ 
            'datas' => $queue, 
            'clinics' => $clinics,
            'postLink'=>'',
            'transactionType' => $transactionType, 
            'transactionData' => $transactionData,
            'AssesAndRec'   =>  $AssesAndRec,
            'PhysicalExaminationReport' => $PhysicalExaminationReport,
            'selectedClasses'   => $selectedClasses,
            'VitalSigns'    => $VitalSigns,
	    'ItemVitals'	=> $itemVitals
            ]);
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
        $Que = DB::connection('CMS')->table('Queue')->where('Id', $id)->first();
	
	 $AssesAndRec = DB::connection('CMS')->table('PEAssesAndRec')
                        ->where('QueueCode', $Que->Code)
                        ->get(['Assessment', 'Recommendation', 'CMS.PEAssesAndRec.ItemCode',  'CMS.PEAssesAndRec.Class']);
	
	$pending = 0;
	$A = 0;
	$B = 0;
	$C = 0;
	$D = 0;
	$MyClass = "";
	foreach($AssesAndRec as $item)
	{
		foreach(json_decode($item->Class ?? '{}', true) as $class)
		{
			if($class == "PENDING")
			{
				$pending = 1;
			}
			else if( $class == "A" )
			{
				$A = 1;
			}
			else if( $class == "B" )
			{
				$B = 1;
			}
			else if( $class == "C" )
			{
				$C = 1;
			}
			else if( $class == "D" )
			{
				$D = 1;
			}
		}
	}
	
	if( $pending == 1)
	{
		$MyClass = "Pending";
	}
	else
	{
		if($D == 1)
		{
			$MyClass = "Class D";
		}
		else
		{
			if($C == 1)
			{
				$MyClass = "Class C";
			}
			else
			{
				if($B == 1)
				{
					$MyClass = "Class B";
				}
				else
				{
					if($A == 1)
					{
						$MyClass = "Class A";
					}
				}
			}
		}
	}
	//dd($MyClass);
        DB::connection('CMS')->table('PhysicalExaminationReport')->where('QueueCode', $Que->Code)->update([
            'Class' => $MyClass
        ]);
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
    
	public function getCompanyList(Request $request)
	{
		//die('dddd');
		$search_arr = $request->get('search');
		$searchValue = $search_arr['value'];

		$sid = $request->get('sid');

		if ($request->ajax()) {
			$model = $this->getCompany(array('fullname'=>$searchValue));
			return DataTables::of($model)->toJson();
		}

	}
	
	public  function getCompany($params = array())
	{
		$lCompa = DB::connection('Eros')->table('Company')
			->where(function($q) use ($params ) {
				$q->where('ResultUploading', 'LIKE', 'Yes');
				if( !empty($params['sid']) && $params['sid'] != "ALL" )
				{
					$q->where('Id', '=', $params['sid'] );
				}
			})
			->get(array('Id','Code','Name','ErosCode','ResultUploading'));

		$return =  array();
		
		foreach($lCompa as $data)
		{
			array_push($return, array(
				'Id'			=> $data->Id
				,'Code'		=> $data->Code
				,'Name'		=> $data->Name
				,'ErosCode'	=> $data->ErosCode
				,'ResultUploading' => $data->ResultUploading
			));
		
		}
		
		return $return;	

	}
	
	
    
}
