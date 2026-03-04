<?php

namespace App\Http\Controllers\hl7;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QueuingEventsController extends Controller
{
    public function reset()
    {
        $currentDate = Carbon::now()->format('Y-m-d');

        DB::connection('Queuing')->select('DELETE FROM Patient');

        DB::connection('Queuing')->select('DELETE FROM Kiosk where `IdBU` LIKE "BAE" ');

        DB::connection('Queuing')->table('QueNumber')->update([
            'Date' => $currentDate,
            'Number' => 0,
        ]);

        return response()->json(['message' => 'Kiosk, Patient and QueNumber reset successfully']);
    }
}
