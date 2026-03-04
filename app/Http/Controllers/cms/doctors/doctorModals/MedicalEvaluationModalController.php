<?php

namespace App\Http\Controllers\cms\doctors\doctorModals;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\cms\Queue;
use App\Models\eros\ErosDB;
use Carbon\Carbon;
class MedicalEvaluationModalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

       return view('cms.doctor.medicalEvalModal');
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
        $vital = DB::connection('CMS')->table('VitalSign')->where('IdQueue', $id)->first();
        $AssesAndRec = DB::connection('CMS')->table('PEAssesAndRec')->where('IdQueue', $id)->where('ItemCode', $_GET['ItemCode'])->get();
        $PEdata = DB::connection('CMS')->table('PhysicalExaminationReport')->where('IdQueue', $id)->get();
        //dd($AssesAndRec);
        $GP = $PEdata[0]->GP ?? '';
        preg_match('/(\d)(\d)\((\d+-\d+-\d+-\d+)\)/', $GP, $matches);
        
        // Assign the values
        $g_value = $matches[1] ?? '';
        $p_value = $matches[2] ?? '';
        $p1_value = $matches[3]?? '';
        
        $AssesmentAndRecomData = DB::connection('Eros')->table('AssesmentCode')->get();

        $assessments = json_decode($AssesAndRec[0]->Assessment ?? '{}', true);
        $recommendations = json_decode($AssesAndRec[0]->Recommendation ?? '{}', true);
        $findings = json_decode($AssesAndRec[0]->Findings ?? '{}', true);
        $class = json_decode($AssesAndRec[0]->Class ?? '{}', true);
        return view('cms.doctor.doctorModals.medicalEvalModal', [
            'vitals' => $vital,
            'assesAndRec' => $AssesAndRec,
            'g_value' => $g_value, 
            'p_value' => $p_value, 
            'p1_value' => $p1_value,
            'PEdata' => $PEdata,
            'Assesment' => $AssesmentAndRecomData,
            'assessments' => $assessments, 
            'recommendations' => $recommendations,
            'findings' => $findings,
            'class' => $class
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
	public function edit($id)
	{
		$Que = DB::connection('CMS')->table('Queue')->where('Id', $id)->first();
		
		$vital = DB::connection('CMS')->table('VitalSign')->where('QueueCode', $Que->Code)->first();
		// dd($_GET['count']);
		$AssesAndRec = DB::connection('CMS')->table('PEAssesAndRec')
                        ->where('QueueCode', $Que->Code)
                        ->where('ItemCode', $_GET['ItemCode'])
                        ->get();
		$assessments = json_decode($AssesAndRec[0]->Assessment ?? '{}', true);
		$recommendations = json_decode($AssesAndRec[0]->Recommendation ?? '{}', true);
		$findings = json_decode($AssesAndRec[0]->Findings ?? '{}', true);
		$class = json_decode($AssesAndRec[0]->Class ?? '{}', true);
		$AssesmentAndRecomData = DB::connection('Eros')->table('AssesmentCode')->get();

		$dataTime = date('YmdHis');
		$pdfData =   DB::connection('CMS')->table('Queue')
			    ->where('Queue.Id', '=', $id)
			    ->where('AccessionNo.ItemCode', $_GET['ItemCode'])
			    ->where('AccessionNo.IdTransaction', '=', $_GET['transid'])
			    ->whereNotnull('AccessionNo.AccessionNo')
			    ->leftjoin('AccessionNo', 'AccessionNo.IdQueue', '=', 'Queue.Id' )
			    ->leftjoin('Eros.Patient', 'CMS.Queue.IdPatient', '=', 'Eros.Patient.Id' )
			    ->groupBy('AccessionNo.AccessionNo')
			    ->limit(1)
			    ->get(array('CMS.AccessionNo.AccessionNo','CMS.Queue.Date','Eros.Patient.Code'))[0];
		$folderDate = str_replace('-','',$pdfData->Date);
        
		$fileName = $pdfData->AccessionNo.'_'.$folderDate.'_'.$pdfData->Code;
		
		$ccs = '<style> .modal-dialog { width: 90% !important; height:100% !important; }</style>';
		$pdfviewer = $ccs . '<iframe width="100%" height="600px" src="http://' . $_SERVER['SERVER_ADDR'] . '/PDF/hPDF/' . $folderDate . '/' . $fileName . '.pdf?' . $dataTime . '" title="PDF Results"></iframe>';
		return view('cms.doctor.doctorModals.medicalEvalModalForPDF', [
		    'vitals' => $vital,
		    'pdfviewer' => $pdfviewer,
		    'Assesment' => $AssesmentAndRecomData,
		    'assesAndRec' => $AssesAndRec,
		    'assessments' => $assessments, 
		    'recommendations' => $recommendations,
		    'findings' => $findings,
		    'class' => $class
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
		$itemCode = $_GET['ItemCode']; 

		if (!$itemCode) {
			return response()->json(['error' => 'ItemCode is required'], 400);
		}

		// Get IdQueue from POST request
		$idQueue = $id; 
		$Que = DB::connection('CMS')->table('Queue')->where('Id', $id)->first();
		// Retrieve array inputs from POST request
		$assessments = $request->input('Assessment', []); 
		$recommendations = $request->input('Recommendation', []);
		$findings = $request->input('Findings', []);
		$classes = $request->input('Class', []);

		// Create associative arrays
		$assessmentData = [];
		$recommendationData = [];
		$findingsData = [];
		$classData = [];

		foreach ($assessments as $index => $assessment) {
			$key = "Assessment" . ($index + 1);
			$assessmentData[$key] = $assessment;
			$findingsData["Findings" . ($index + 1)] = $findings[$index] ?? ''; // Handle missing values
			$classData["Class" . ($index + 1)] = $classes[$index] ?? '';
		}

		foreach ($recommendations as $index => $recommendation) {
			$key = "Recommendation" . ($index + 1);
			$recommendationData[$key] = $recommendation;
		}

		// Convert arrays to JSON
		$assessmentJson = json_encode($assessmentData, JSON_UNESCAPED_UNICODE);
		$recommendationJson = json_encode($recommendationData, JSON_UNESCAPED_UNICODE);
		$findingsJson = json_encode($findingsData, JSON_UNESCAPED_UNICODE);
		$classJson = json_encode($classData, JSON_UNESCAPED_UNICODE);

		// Update or insert into the database
		DB::connection('CMS')->table('PEAssesAndRec')->updateOrInsert(
			[
			'QueueCode' => $Que->Code, 
			'ItemCode' => $itemCode
			],
			[
			'IdQueue'  => $idQueue,
			'Findings'       => $findingsJson,  // JSON for Findings
			'Class'          => $classJson,     // JSON for Class
			'Assessment'     => $assessmentJson, 
			'Recommendation' => $recommendationJson
			]
		);
		DB::connection('CMS')->commit(); 
		return response()->json(['success' => 'Data saved successfully'], 200);
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
