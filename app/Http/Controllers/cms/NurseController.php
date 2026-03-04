<?php

namespace App\Http\Controllers\cms;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NurseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
		$queue = \App\Models\Queue::todaysQueue()->get();
        return view('cms.nurse', ['queue' => $queue]);
        
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $clinics = Auth::user()->getUserClinics()->orderBy('Clinics.Code', 'asc')->get(array('Clinics.Id','Clinics.Code'));
		$queue = \App\Models\Queue::todaysQueueID($id)->get();
		$trans = \App\Models\Transactions::getTransactionByQueue($queue[0]->Id);
		
     	return view('cms.nurseEdit', ['trans' => $trans,'postLink' => url('/cms/queue/'.$id),'clinics' => $clinics, 'defaultClinic' => $queue[0]->IdClinic, 'datas' => $queue[0]  ]);    
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
