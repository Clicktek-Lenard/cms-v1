<?php

namespace App\Http\Controllers\cms\pages\queue;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\cms\Verification;
use Illuminate\Database\QueryException;

class QueueCardValidationController extends Controller
{
    public function checkCardNumber(Request $request)
    {
        $discount = str_replace('-', '', $request->input('Discount'));

        $userBU = session('userClinicCode');
        $cardNumber = str_replace('-', '', $request->input('CardNumber1'));
        $itemused = $request->input('itemPrice1');
        $qtycounts = DB::connection('Eros')->table('CardEnrollment')->where('ReleaseTo', $userBU)->count();
        $Transqty = DB::connection('CMS')->table('Transactions')->where('HCardNumber', $cardNumber)->where('Status', '>=', '201' )->where('ItemUsedItemPrice', $itemused)->count();
        $cardsold = DB::connection('CMS')->table('Transactions')->where('HCardNumber', $cardNumber)->where('Status', '>=', '201' )->where('ItemUsedItemPrice', 0)->where('GroupItemMaster', 'CARD')->exists();
        $cardsolds = DB::connection('Eros')->table('CardEnrollment')->where('CardNumber', $cardNumber)->get(array('CardNumber'));
        $Trans = DB::connection('CMS')->table('Transactions')->where('HCardNumber', $cardNumber)->where('Status', '>=', '201' )->where('ItemUsedItemPrice', $itemused)->where('GroupItemMaster', 'CARD')->first();
        $count = DB::connection('CMS')->table('Transactions')->where('HCardNumber', $cardNumber)->where('ItemUsedItemPrice', '>', 0)->where('GroupItemMaster', 'CARD')->Count();
        $transactions = DB::connection('CMS')->table('Transactions')
        ->leftJoin('CMS.Queue', 'CMS.Queue.Id', '=', 'CMS.Transactions.IdQueue')
        ->leftJoin('Eros.Patient', 'Eros.Patient.Id', '=', 'CMS.Queue.IdPatient')
        ->leftJoin('Eros.ItemPrice', 'Eros.ItemPrice.Id', '=', 'CMS.Transactions.IdItemPrice')
        ->where('HCardNumber', $cardNumber)
        ->where('ItemUsedItemPrice', '<>', 0)
        ->where('CMS.Transactions.Status', '>=', '201' )
        ->get(array('Eros.Patient.FullName as FullName', 'CMS.Queue.AgePatient', 'CMS.Transactions.Date as Date', 'Eros.ItemPrice.ItemUsed as ItemUsed' ));
        $html = ''; 

    foreach ($transactions as $transaction) {
    $html .= '<div class="row form-group row-md-flex-center">';
    $html .= '<div class="col-sm-2 col-md-2 pad-left-0-md text-right-md">';
    $html .= '<label class="bold">Name:</label>';
    $html .= '</div>';
    $html .= '<div class="col-sm-9 col-md-9">';
    $html .= '<input type="text" class="form-control" value="' . $transaction->FullName . '" readonly="readonly">';
    $html .= '</div>';
    $html .= '</div>';

    $html .= '<div class="row form-group row-md-flex-center">';
    $html .= '<div class="col-sm-2 col-md-2 pad-left-0-md text-right-md">';
    $html .= '<label class="bold">Transaction Date:</label>';
    $html .= '</div>';
    $html .= '<div class="col-sm-9 col-md-9">';
    $html .= '<input type="text" class="form-control" value="' . $transaction->Date . '" readonly="readonly">';
    $html .= '</div>';
    $html .= '</div>';
     $html .= '<hr>';
    }
        $notexistingCard = DB::connection('Eros')->table('CardEnrollment')
            ->select('CardNumber', 'BusinessUnits.Description', 'ReleaseTo', 'CMS.Transactions.IdItemPrice', 'CMS.Transactions.HCardNumber', 'Eros.Patient.FullName as FullName', 'CMS.Queue.AgePatient', 'CMS.Transactions.Date as Date', 'CMS.Queue.Code as Code')
            ->leftJoin('BusinessUnits', 'Code', '=', 'ReleaseTo')
            ->leftJoin('CMS.Transactions', 'CMS.Transactions.HCardNumber', '=', 'CardNumber')
            ->leftJoin('CMS.Queue', 'CMS.Queue.Id', '=', 'CMS.Transactions.IdQueue')
            ->leftJoin('Eros.Patient', 'Eros.Patient.Id', '=', 'CMS.Queue.IdPatient')
            ->leftJoin('ItemPrice', 'CMS.Transactions.IdItemPrice', '=', 'ItemPrice.Id')
            ->where('CardNumber', $cardNumber)
            ->first();
        // dd($cardsold);
        // dd();
        if($Trans)  
        {
            if($itemused == 0){
           return response()->json([
                'status' => 'error', 
                'message' => '<br><div class="row form-group row-md-flex-center"><div class="col-sm-2 col-md-2 pad-left-0-md text-right-md"><label class="bold">Queue Number:</label></div><div class="col-sm-9 col-md-9"><input type="text" class="form-control" value="' . $notexistingCard->Code . '" readonly="readonly"></div></div><div class="row form-group row-md-flex-center"><div class="col-sm-2 col-md-2 pad-left-0-md text-right-md"><label class="bold">Transaction Date:</label></div><div class="col-sm-9 col-md-9"><input type="text" class="form-control" value="' . $notexistingCard->Date . '" readonly="readonly"></div></div>',
                'title' => 'Health plus Card Number is Already Assigned to'
            ]);
                
            }
        }
       
        //    dd( $cardsold);
        if ($notexistingCard) 
        {
            if (!$cardsold && $itemused !=0)
            {
                return response()->json(['status' => 'error', 'message' => 'Card is not yet sold']);
            }
            if ($itemused == $count && $itemused !=0) {
                return response()->json([
                      'status' => 'error',
                      'message' => $html,
                      'title' => 'Your Free Availment is Already used'
                  ]); 
              }
           if (property_exists($notexistingCard, 'ReleaseTo') && $notexistingCard->ReleaseTo == $userBU) 
            {
                return response()->json(['status' => 'success']);

            } else {
                if($itemused == 0){
                return response()->json([
                    'status' => 'error', 
                    'title' => 'Error', 
                    'message' => '<p>Card Number is Resgisted to <strong>'. $notexistingCard->Description .'</stong>']);
                }
                
            }
    
        } 
        else 
        {
            return response()->json([
                'status' => 'error', 
                'message' => 'Card number does not exist. Please check and try again.',
                'title' => 'Error'
            ]);
               
           
        }
        
    }

    public function discountValidation(Request $request)
    {
        $cardNumber = str_replace('-', '', $request->input('Discount'));
        $userBU = session('userClinicCode');
        $Transacation = DB::connection('CMS')->table('Transactions')->where('HCardNumber', $cardNumber)->where('Status', '>=', '201')->where('GroupItemMaster', 'CARD')->first();
        // dd($Transacation);
            if($Transacation)
            {
              
                    return response()->json(['status' => 'success']);
    
                
            }else {

                return response()->json([
                    'status' => 'error', 
                    'message' => 'Your card number has not been assigned or sold. Please check and try again.',
                    'title' => 'Error'
                ]); 
             }
           
    }
    
} 