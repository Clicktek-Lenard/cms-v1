<?php

namespace App\Http\Controllers\cms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use App\Models\cms\Queue;
use App\Models\cms\Transactions;
use App\Models\eros\ErosDB;

use DataTables;

class BranchManagerModuleController extends Controller
{

    public function index(Request $request)
    {	
        $queue = Queue::BMtodaysQueue()
        
            ->get(array('CMS.Queue.Id','CMS.Queue.Code','CMS.Queue.Date','Eros.Patient.FullName','CMS.QueueStatus.Name as QueueStatus','CMS.Queue.InputBy','CMS.Queue.AnteDateReason'));
            $queueCount = $queue->count();

            session(['queueCount' => $queueCount]);

        return view ('cms.bmModule', ['queue' => $queue]);
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
        //edit function
    }
	


  
}
