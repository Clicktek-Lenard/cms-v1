<?php

namespace App\Http\Controllers\cms\pages\enrollment;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\cms\CardEnrollment;

class EnrollmentReceivedController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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
        $datas = CardEnrollment::getInfo($id);
    
        $users = CardEnrollment::getUsers();

        return view('cms/pages.enrollmentReceived', ['postLink'=>url(session('userBUCode').'/enrollment/pages/enrollmentReceived/'.$id), 'datas' => $datas, 'users' => $users]);
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

            DB::connection('Eros')->table('CardEnrollment')
                ->where('Id', $id)
                ->update([
                    'Status'        => '1',
                    'ReceivedBy'    => Auth::user()->username,
                    'ReceivedDate'  => date('Y-m-d H:i:s')
                ]);
    
            return response()->json(['message' => 'ReceivedBy updated successfully']);
     
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
