<?php

namespace App\Http\Controllers\cms\doctors\doctorModals;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\cms\Queue;
use App\Models\eros\ErosDB;
use Carbon\Carbon;
class PcpDoctorModalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        dd('index test');
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
		DB::connection('CMS')->beginTransaction();
		DB::connection('CMS')->table('VitalSign')->where('IdQueue', $_GET['IdQueue'])->update(['UpdateBy'=>Auth::user()->username]);
		DB::connection('CMS')->table('AccessionNo')->where('IdQueue', $_GET['IdQueue'])->where('Type', 'CLINIC')->update(['Status' => '280']);
		DB::connection('CMS')->table('VitalSign')->updateOrInsert(
			['IdQueue'          =>      $_GET['IdQueue']],
			['ChiefComplaint'   =>      $request->input('ChiefComplaint')
			,'PulseRate'        =>      $request->input('PulseRate')
			,'RespiratoryRate'  =>      $request->input('Respiraroty')
			,'BloodPresure'     =>      $request->input('BloodPresure')
			,'BloodPresureOver' =>      $request->input('BloodPresureOver')
			,'BloodPresure2'    =>      $request->input('BloodPresure2')
			,'BloodPresureOver2'=>      $request->input('BloodPresureOver2')
			,'BloodPresure3'    =>      $request->input('BloodPresure3')
			,'BloodPresureOver3'=>      $request->input('BloodPresureOver3')
			,'Temperature'      =>      $request->input('Temperature')
			,'Height'           =>      $request->input('Height')
			,'Weight'           =>      $request->input('Weight')
			,'BMI'              =>      $request->input('BMI')
			,'UcorrectedOD'     =>      $request->input('unCorOD')
			,'UcorrectedOS'     =>      $request->input('unCorOS')
			,'CorrectedOD'      =>      $request->input('CorOD')
			,'CorrectedOS'      =>      $request->input('CorOS')
			,'UncorrectedNearOD'=>      $request->input('UncorrectedNearOD')
			,'UncorrectedNearOS'=>      $request->input('UncorrectedNearOS')
			,'CorrectedNearOD'  =>      $request->input('CorrectedNearOD')
			,'CorrectedNearOS'  =>      $request->input('CorrectedNearOS')
			,'Deficient'        =>      $request->input('Deficient')
			,'ColorVision'      =>      $request->input('ColorVision')
			,'WithContactLens'  =>      $request->input('WithLense')
			,'WithEyeGlass'     =>      $request->input('WithEyeglass')
			,'InputBy'          =>      Auth::user()->username
		]);
		DB::connection('CMS')->table('Queue')->where('Id', $_GET['IdQueue'])->where('Status', '230')->update([
			'Status'            =>          '280'
		]);
		DB::connection('CMS')->commit(); 
		return $_GET['IdQueue'];
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        dd('show');
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
        $Physician = DB::connection('Eros')->table('Physician')->where('Status', 'Active')->limit(10)->get(['Code', 'FullName' , 'Description', 'Id']);
       // dd($Physician);
        return view('cms.doctor.doctorModals.doctorPCPModal',['Physician' => $Physician]);
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
         dd($id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function getPhysicians(Request $request)
{ //dd($request->input('q', ''));
    $query = $request->input('q', ''); // Get the search query
    $physicians = DB::connection('Eros')->table('Physicians')
        ->where('Status', 'Approved')
        ->where('FullName', 'like', '%' . $query . '%') // Filter by query
	->whereIn('SubGroup', ['PCP', 'SPL'])
        ->limit(100) // Limit results
        ->get(['Code', 'FullName', 'Id']); // Fetch only necessary fields
//dd($physicians);
    return response()->json($physicians);
}


}
