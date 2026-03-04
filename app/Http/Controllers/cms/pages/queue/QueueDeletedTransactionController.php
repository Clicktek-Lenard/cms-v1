<?php

namespace App\Http\Controllers\cms\pages\queue;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class QueueDeletedTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
	
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
	
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {   
        //data for patient information//
        $data = DB::connection('CMS')->table('Queue')
		->leftJoin('Eros.Patient', 'Queue.IdPatient', '=', 'Eros.Patient.Id')
                ->where('CMS.Queue.Id', $id)
                ->get(array('Eros.Patient.FullName', 'CMS.Queue.Code as QCode', 'CMS.Queue.Date as Date'))[0];

        //data history for datatables//
        $payHistory = DB::connection('CMS')->select("
				SELECT `p_pn`.`Code`, `p_pn`.`Description`, `p_pn`.`UpdateDateTime`, `p_pn`.`UpdateBy`, `p_pn`.`DeletedReason`, `p_pn`.`Status`
				from
				(	(select  tb2.`CodeItemPrice` as  'Code', tb2.`DescriptionItemPrice` as 'Description',  tb2.`UpdateDateTime`, tb2.`UpdateBy`, tb1.`DeletedReason`, tb2.`Status`
						from `PaymentHistory`tb1 LEFT JOIN `TransactionsDeleted` tb2 ON(tb1.`IdTransaction` = tb2.`Id`) where tb1.`IdQueue` = '".$id."' and tb1.`Status` = 2)
				UNION
					(select  tb1.`CodeItemPrice` as  'Code', tb1.`DescriptionItemPrice` as 'Description',  tb1.`UpdateDateTime`, tb1.`UpdateBy`, tb1.`Token`, tb1.`Status`
						from `TransactionsDeleted` tb1 where tb1.`IdQueue` = '".$id."' and tb1.`Status` = 201)
				)  as `p_pn`	
			");
	
		
              
        
                    //dd($payHistory);
        return view('cms/pages.queueDeletedTransaction',['data'=> $data, 'payHistory' => $payHistory]);
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
	//put here for temp update
	
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
