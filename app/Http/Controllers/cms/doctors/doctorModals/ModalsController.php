<?php

namespace App\Http\Controllers\cms\doctors\doctorModals;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\cms\Queue;
use App\Models\eros\ErosDB;
use Carbon\Carbon;
class ModalsController extends Controller
{
    public function PcpModal(Request $request)
    {
        //
    }
    
    public function EditOrderModal($id)
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
    return view('cms.doctor.doctorModals.doctorOrderModal', ['data' => $datas, 'physicianorder' => $PhysicianOrder]);
    }

    public function SaveOrderModal(Request $request)
    {
        DB::connection('CMS')->table('PhysicianOrder')->where('IdQueueFrom', $_GET['idQueue'])->delete();
        if(is_array($request->input('ItemCode'))){
            foreach($request->input('ItemCode') as $itemCode){
            $Code = DB::connection('Eros')->table('ItemMaster')->where('Code', $itemCode)->get(['Code'])[0];
                
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
    }

    public function PastResult($id)
    {

        $Result = DB::connection('CMS')->table('Transactions')
                ->leftJoin('Queue', 'Transactions.IdQueue', '=', 'Queue.Id')
                ->leftJoin('QueueStatus', 'Queue.Status', '=', 'QueueStatus.Id')
                ->where('IdQueue', $id)
                ->get(array('Transactions.CodeItemPrice', 'Transactions.DescriptionItemPrice', 'QueueStatus.Name as QueueStatus', 'Queue.Status', 'Queue.AnteDate')); 
                //dd($Result);
        return view('cms.doctor.doctorModals.PastResultModal',['pastResult' => $Result]);
    }

}
