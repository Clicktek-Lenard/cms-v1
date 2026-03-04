<?php

namespace App\Http\Controllers\cms\doctors\doctorModals;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\cms\Queue;
use App\Models\eros\ErosDB;
use Carbon\Carbon;
class DoctorsOrderModalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
      
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
		DB::connection('CMS')->table('PhysicianOrder')->where('IdQueueFrom', $_GET['idQueue'])->delete();
		if(is_array($request->input('ItemCode'))){
			foreach($request->input('ItemCode') as $itemCode){
				$Code = DB::connection('Eros')->table('ItemMaster')->where('Code', $itemCode)->get(['Code'])[0];
				//dd($itemCode);
				DB::connection('CMS')->table('PhysicianOrder')->insert([
				'IdQueueFrom'       =>      $request->input('IdQueue')
				,'IdQueueTo'        =>      $request->input('IdQueue')
				,'IdPatient'        =>      $request->input('IdPatient')
				,'IdDoctor'         =>      $request->input('IdDoctor')
				,'ItemCode'         =>      $Code->Code
				,'DateOrder'        =>      date('Y-m-d')
				,'Status'           =>      '1'
				]);
			}
		}
		DB::connection('CMS')->commit(); 
	}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return 'Testing';
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
{
    
    $datas = DB::connection('Eros')->table('ItemMaster')
        ->leftJoin('CMS.PhysicianOrder', 'Eros.ItemMaster.Code', '=', 'CMS.PhysicianOrder.ItemCode')
        ->select(
            'Eros.ItemMaster.Code as ItemCode', 
            'Eros.ItemMaster.Description', 
            'CMS.PhysicianOrder.IdQueueFrom',
            'CMS.PhysicianOrder.Status as Status'
        )
        ->where(function($query) use ($id) {
            $query->where('ItemStatus', 'Active')
                  ->where('Type', 'Item')
                  ->orWhere('CMS.PhysicianOrder.IdQueueFrom', $id);
        })
        ->groupby('Eros.ItemMaster.Code')
        ->get();
        $PhysicianOrder = DB::connection('CMS')->table('PhysicianOrder')->where('IdQueueFrom', $id)->get(['ItemCode']);
        
    //dd(json_encode($PhysicianOrder));
    return view('cms.doctor.doctorModals.doctorOrderModal', ['data' => $datas, 'physicianorder' => $PhysicianOrder]);
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
        return 'Update';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function getAjax()
    {
       //
    }
}
