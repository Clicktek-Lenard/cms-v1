<?php

namespace App\Http\Controllers\cms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\cms\CardNumber; // Import the CardNumber model
use App\Http\Controllers\Controller;
use Milon\Barcode\DNS1D;

class CardNumberGenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('cms.cardnumberGenerator');
    }

    /**
     * Store the generated card numbers in the database.
     *
     * @param Request $request
     * @return Response
     */
    //public function generateBarcode($cardNumber)
    //{
    //    $barcode = new DNS1D();
    
    //    $barcodeImage = $barcode->getBarcodeSVG($cardNumber, 'C128');

    //    return $barcodeImage;
    //}
    public function getLastBatch()
    {
        $lastBatch = CardNumber::max('Batch'); // Get the maximum Batch from the database
        
        return response()->json(['lastBatch' => $lastBatch]);
    }    
    
    public function getLastSeriesNumber()
    {
        $lastSeries = CardNumber::max('SeriesNum'); // Get the maximum SeriesNum from the database

        return response()->json(['lastSeries' => $lastSeries]);
    }
    
    public function store(Request $request)
    {
        $year = $request->input('year');
        $batch = $request->input('batch');
        $month = $request->input('month');
        $series = $request->input('seriesnum');
        $maskedseries = $request->input('maskedseriesnum');
        $cardNumber = $request->input('card_number');

        // Create a new CardNumber instance
        $card = new CardNumber();
        $card->Year = $year;
        $card->Batch = $batch;
        $card->Month = $month;
        $card->SeriesNum = $series;
        $card->MaskedSeries = $maskedseries;
        $card->GeneratedCardNumber = $cardNumber;

        // Save the card number to the database
        $card->save();

        return response()->json(['message' => 'Card number has been saved.']);
    }

}

