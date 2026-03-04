<?php

namespace App\Http\Controllers\cms\api;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class QueuePhysicianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {	//
        // return session('userClinicDefault');
      $queue = Queue::todaysQueue()->get(array('CMS.Queue.Id','CMS.Queue.Code','Eros.Physician.PRCNo','Eros.Physician.FullName','Eros.Physician.LastName','Eros.Physician.FirstName','CMS.QueueStatus.Name as QueueStatus','CMS.Queue.InputBy','CMS.Queue.Notes'));
      return view('cms.queue', ['queue' => $queue]);
      //return view('cms.queue')->withErrors([ 'error' => 'my error']);
  }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return 'ricky';
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
        return DB::connection('Eros')->table('Physician')
            // ->where('Status', '=', 'Active') // Filter by active status
            ->where(function($query) use ($id) {
                $query->where('PRCNo', 'like', $id.'%')
                      ->orWhere('LastName', 'like', $id.'%')
                      ->orWhere('FirstName', 'like', $id.'%')
                      ->orWhere('MiddleName', 'like', $id.'%');
            })
            ->get(['Id', 'PRCNo', 'FullName', 'Code', 'LastName', 'FirstName', 'MiddleName', 'Description']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
   {
    return DB::connection('Eros')->table('Physician')
    // ->where('Status', '=', 'Active') // Filter by active status
    ->where(function($query) use ($id) {
        $query->where('PRCNo', 'like', $id.'%')
              ->orWhere('LastName', 'like', $id.'%')
              ->orWhere('FirstName', 'like', $id.'%')
              ->orWhere('MiddleName', 'like', $id.'%');
    })
    ->get(['Id', 'PRCNo', 'FullName', 'Code', 'LastName', 'FirstName', 'MiddleName', 'Description']);
		
    }

 public function storePhysicianData(Request $request, $id)
    {
        $dirtyData = $request->_dirty;

        if (!$dirtyData) {
            return response()->json(['error' => 'No dirty data received'], 400);
        }

        $newLogs = json_decode($dirtyData, true);
        $existingData = DB::connection('Eros')->table('Physician')
            ->where('Id', $id)->select('ApprovalLogs')->first();

        $existingLogs = [];
        if ($existingData && $existingData->ApprovalLogs) {
            $existingLogs = json_decode($existingData->ApprovalLogs, true);
        }

        $user = Auth::user()->username;
        $now = now()->toDateTimeString();

        foreach ($newLogs as $key => $value) {
            $existingLogs[$key] = [
                'oldVal'     => $value['oldVal'] ?? '',
                'newVal'     => $value['newVal'] ?? '',
                'updatedBy'  => $user,
                'updateTime' => $now,
            ];
        }

        DB::connection('Audit')->table('ErosPhysicianInfo')->insert([
            'IdPhysician' => $id,
            'Logs' => $dirtyData,
            'SystemDateTime' => now(),
        ]);

        DB::connection('Eros')->table('Physician')
            ->where('Id', $id)
            ->update([
                'ApprovalLogs' => json_encode($existingLogs),
                'SystemUpdateTime' => now(),
                'UpdateBy' => $user,
            ]);

        return response()->json(['message' => 'Dirty data logged successfully']);
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
