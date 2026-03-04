<?php
    
namespace App\Http\Controllers\cms;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\cms\Verification;
use Illuminate\Database\QueryException;

class VerifiedNumbersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {	
        // $queue = Verification::verifyNumber();

        $data = Verification::getInfo(); 

		return view('cms.verifiedNumbers', ['data' => json_encode ($data)]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $users = Verification::getUsers();

        $datas = Verification::getInfo();
    
        return view('cms.cardVerifyingCreate', ['users' => $users, 'datas' => $datas, 'defaultClinic' => session('userClinicCode')]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $cardNumber = $request->input('VerifiedCardNumbers');
        $message = '';
    
        // Check if CardNumber already exists in CardKey table
        $cardExists = DB::connection('Eros')->table('CardKey')
            ->where('GeneratedCardNumber', $cardNumber)
            ->exists();
    
        if ($cardExists) {
            // CardNumber exists in CardKey, fetch it
            $cardKeyData = DB::connection('Eros')->table('CardKey')
                ->where('GeneratedCardNumber', $cardNumber)
                ->first();
    
            // Check if the card is already verified in CardVerified table
            $cardVerifiedExists = DB::connection('Eros')->table('CardVerified')
                ->where('VerifiedCardNumbers', $cardNumber)
                ->exists();
    
                if (!$cardVerifiedExists) {
                    // If not verified, update CardVerified table
                    $result = DB::connection('Eros')->table('CardVerified')
                        ->updateOrInsert(
                            ['VerifiedCardNumbers' => $cardNumber],
                            [
                                'ICTReceived' => Auth::user()->username,
                                'DateReceived' => date('Y-m-d H:i:s'),
                            ]
                        );
                
                    if ($result) {
                        $message = "Card has been verified and updated successfully.";
                    } //else {
                    //     $message = "Data update failed.";
                    // }
                } else {
                    $message = 'CARD NUMBER ALREADY EXISTED';
                }
        } else {
            $message = 'Please scan Another Card Number';
        }
        $data = Verification::getInfo();

        return view('cms.verifiedNumbers', ['data' => $data, 'defaultClinic' => session('userClinicCode'), 'alertMessage' => $message]);
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
    //
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
        DB::connection('Eros')->table('CardVerified')
        ->where('Id', $id)
        ->update([
           'DateReceived'     => date('Y-m-d H:i:s'),
           'ICTReceived'  => Auth::user()->username
       ]);

       $queue = Verification::getInfo();

       return $this->edit($id);
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
