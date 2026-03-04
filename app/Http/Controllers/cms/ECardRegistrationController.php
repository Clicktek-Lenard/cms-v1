<?php

namespace App\Http\Controllers\cms;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\cms\CardEnrollment;
use Illuminate\Database\QueryException;


class ECardRegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {	
		$queue = CardEnrollment::registrationData();
        $users = CardEnrollment::getUsers();
     
		return view('cms.ecardRegistration', ['users' => $users, 'defaultClinic' => session('userClinicCode'), 'queue' => json_encode ($queue)]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
       $users = CardEnrollment::getUsers();
    
       return view('cms.ecardCreate', ['users' => $users, 'defaultClinic' => session('userClinicCode')]);
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $cardNumber = $request->input('CardNumber');
        $message = ''; // Initialize the message variable
    
        // Check if CardNumber already exists
        $cardExists = DB::connection('Eros')->table('CardVerified')
            ->where('VerifiedCardNumbers', $cardNumber)
        
            ->exists();

        //If cardnumber already exists in Enrollment pass a message
        $cardAlreadyExist = DB::connection('Eros')->table('CardEnrollment')
            ->where('CardNumber', $cardNumber)
            ->exists();
    
        if ($cardAlreadyExist) {

            // CardNumber exists, display a message for duplicate entry
              $message = 'Card number already exists. Please choose a different Card number.';

        } elseif($cardExists) {       

            // CardNumber exists, update the specified columns
            DB::connection('Eros')->table('CardEnrollment')
                ->insert([
                    'CardNumber'    => $cardNumber,
                    'ReleaseTo'     => $request->input('Users'),
                    'DateEnrolled'  => date('Y-m-d H:i:s'),
                    'DateRelease'   => date('Y-m-d H:i:s'),
                    'ReleaseBy'     => Auth::user()->username,
                    'Status'        => '0'
                ]);

                $message = "Data has been enrolled successfully...";

        } else {

                $message = 'Card number doesn\'t exist. Please contact your administrator.';
        }
    
        $queue = CardEnrollment::registrationData();
        $users = CardEnrollment::getUsers();
    
        return view('cms.ecardRegistration', ['users' => $users, 'defaultClinic' => session('userClinicCode'), 'alertMessage' => $message, 'queue' => json_encode ($queue)]);
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
       
        $datas = CardEnrollment::getInfo($id);
    
        $users = CardEnrollment::getUsers();

        return view('cms.enrollmentEdit', ['datas' => $datas, 'users' => $users, 'defaultClinic' => session('userClinicCode'), 'postLink'=>url(session('userBUCode').'/enrollment/cardregistration/'.$id)]);
    
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
