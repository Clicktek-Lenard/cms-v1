<?php

namespace App\Http\Controllers\cms\pages\queue;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Database\Query\Builder;

class EditOrController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }
    public function edit(Request $request, $id)
    {
        $EditOr = DB::connection('CMS')
            ->table('Transactions')
            ->leftJoin('PaymentHistory', 'Transactions.Id', '=', 'PaymentHistory.IdTransaction')
            ->where('Transactions.IdQueue', $id)
            ->where('PaymentHistory.ORNum', '!=', null)
            ->where('PaymentHistory.CoverageType', '!=', 'HMO')
            ->select('PaymentHistory.ORNum', 'DescriptionItemPrice', 'CodeItemPrice', 'Transactions.Id', 'PriceGroupItemPrice')
            ->groupby('Transactions.Id')
            ->get();


        $PaymentEditOr = DB::connection('CMS')->table('PaymentHistory')
                        ->leftJoin('CMS.Queue', 'CMS.PaymentHistory.IdQueue', '=', 'CMS.Queue.Id')
                        ->leftJoin('Eros.Patient', 'Queue.IdPatient', '=', 'Eros.Patient.Id')
                        ->leftJoin('CMS.Transactions', 'CMS.Queue.Id', "=", 'CMS.Transactions.IdQueue')
                        ->where('CMS.PaymentHistory.IdQueue', $id)
                        ->groupby('CMS.PaymentHistory.IdQueue')
                        ->get(array('Eros.Patient.FullName', 'CMS.Queue.Code as QCode', 'CMS.Queue.Date as Date'))[0];
                      // dd($EditOr);
        return view('cms/pages.EditOR',['PaymentEditOr'=> $PaymentEditOr, 'Trans' => $EditOr]);
    }
   
    public function update(Request $request, $id)
    {
        $ORNumber = $request->input('ORNum');
        $Id = $request->input('Id');
        //dd($Id);
        $EditedOR = DB::connection('CMS')->table('PaymentHistory')->where('IdTransaction', $Id)->update([
            'ORNum' => $request->input('ORNum'),
            'UpdateBy' => Auth::user()->username,
            'PrevOR' => $request->input('PrevOR'),
            'OReditReason' => $request->input('Reasons'),
        ]);
         
    }
    
    
    public function show()
    {

    }
    
    
}
