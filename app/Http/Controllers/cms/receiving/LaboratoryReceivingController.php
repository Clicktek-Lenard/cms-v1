<?php

namespace App\Http\Controllers\cms\receiving;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\eros\ErosDB;
use App\Models\cms\Kiosk;

use App\Http\Controllers\Controller;
use App\Models\cms\Queue;
use Illuminate\Support\Facades\Auth;
use App\Models\cms\Receiving;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Response;

class LaboratoryReceivingController extends Controller
{
    public function index()
    {
//         $toReceive = Queue::specimenReceiving()
//             ->where('Type', '=', 'LABORATORY')
//             ->get();
// dd($toReceive);
        $rejectionData = ErosDB::getRejectionData();

        return view('cms.receiving.laboratoryReceiving', ['rejectiondata' => $rejectionData]);
    }

    public function edit($id)
    {

    }
    
    public function show($id)
    { 

    }

    public function receive(Request $request)
    {
        $receivingData = $request->json('receiving_data', []);

        if (empty($receivingData)) {
            return response()->json(['success' => false, 'message' => 'No specimen selected']);
        }

        $itemSubGroup = $receivingData[0]['ItemSubGroup'] ?? null;

        if ($itemSubGroup === 'NonBlood') {
            foreach ($receivingData as $row) {
                if ($row['isRejected'] === true) {
                    DB::connection('CMS')->table('Receiving')
                        ->where('Id', $row['ReceivingId'])
                        ->update([
                            'RejectBy'   => Auth::user()->username,
                            'Reason'              => $row['RejectReasonCode'],
                            'RejectDateTime' => now(),
                            'SendOutStatus'       => 'Rejected',
                        ]);
                    
                    DB::connection('CMS')->table('AccessionNo')
                        ->where('QueueCode', $row['QueueCode'])
                        ->where('ItemCode', $row['ItemCode'])
                        ->update(['Status' => 877]);

                } else {
                    // NONBLOOD - SPECIMEN RECEIVE
                    DB::connection('CMS')->table('Receiving')
                        ->where('Id', $row['ReceivingId'])
                        ->update([
                            'SendoutReceivedBy'   => Auth::user()->username,
                            'SendoutReceivedDate' => now(),
                            'SendOutStatus'       => 'Specimen Received',
                        ]);
                }
            }
        } elseif ($itemSubGroup === 'BLOOD') {
            foreach ($receivingData as $row) {
                $receivingBatchCode = $row['ReceivingBatchCode'];
                $queueCode = $row['QueueCode'];
                $rejectItems = $row['RejectItems'];
                $remainingItems = $row['RemainingItems'];
                $tubeColor = $row['RejectReason']['tubeColor'] ?? null;

                $records = DB::connection('CMS')->table('Receiving')
                    ->where('ReceivingBatchCode', $receivingBatchCode)
                    ->where('QueueCode', $queueCode)
                    ->get();

                foreach ($records as $record) {
                    $itemCode = $record->ItemCode;

                    $tubesSent = json_decode($record->TubesSent, true);
                    $tubesReject = json_decode($record->TubesReject, true);

                    $tubeColorKey = strtolower($tubeColor); // convert "Yellow" => "yellow"
                    $tubesSent[$tubeColorKey]   = $row['TubeRemaining']; 
                    $tubesReject[$tubeColorKey] = $row['TubeReject'];

                    if (in_array($itemCode, $rejectItems) && $tubeColor) {
                        DB::connection('CMS')->table('Receiving')
                            ->where('Id', $record->Id)
                            ->update([
                                'Status'         => 'Rejected',
                                'Reason'         => $row['RejectReason']['rejectionReason'] ?? null,
                                'RejectBy'       => Auth::user()->username,
                                'RejectDateTime' => now(),
                            ]);

                        // LAGAY KO DITO YUNG ACCESSION UPDATE 877
                        DB::connection('CMS')->table('AccessionNo')
                            ->where('QueueCode', $queueCode)
                            ->where('ItemCode', $itemCode)
                            ->update([
                                'Status' => 877,
                            ]);
                    }

                    // Handle remaining items (update status to 'Specimen Received')
                    if (in_array($itemCode, $remainingItems)) {
                        DB::connection('CMS')->table('Receiving')
                            ->where('Id', $record->Id)
                            ->update([
                                'SendOutStatus' => 'Specimen Received',  // Update the SendOutStatus
                                'SendoutReceivedBy'    => Auth::user()->username,
                                'SendoutReceivedDate' => now(),
                            ]);
                    }

                    DB::connection('CMS')->table('Receiving')
                        ->where('ReceivingBatchCode', $receivingBatchCode)
                        ->update([
                            'TubesSent'      => json_encode($tubesSent),
                            'TubesReject'    => json_encode($tubesReject),
                        ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Specimens successfully received.']);
    }


    public function fetch(Request $request)
    {
        $batchCode = $request->input('batch_code');

        $batch = DB::connection('CMS')->table('Transport')->where('BatchCode', $batchCode)->first();

        if (!$batch) {
            return response()->json([
                'success' => false,
                'message' => 'Batch not found'
            ]);
        }

        // Update only if ArrivalDateTime is still empty/zero
        if ($batch->ArrivalDateTime == '0000-00-00 00:00:00' || empty($batch->ArrivalDateTime)) {
            DB::connection('CMS')->table('Transport')
                ->where('BatchCode', $batchCode)
                ->update([
                    'ArrivalDateTime' => now(),
                    'Status' => 'Arrived'
                ]);

            // Refresh $batch after update
            $batch = DB::connection('CMS')->table('Transport')->where('BatchCode', $batchCode)->first();
        }

        // --- Non-blood ---
        $nonBloodData = Queue::laboratoryNonBloodReceiving($batchCode)->get()->map(function ($item) {
            return [
                'ReceivingId' => $item->ReceivingId,
                'IdQueue' => $item->IdQueue,
                'QueueCode' => $item->QueueCode,
                'QFullName' => $item->QFullName,
                'ItemCode' => $item->ItemCode,
                'ItemDescription' => $item->ItemDescription,
                'SendoutBy' => $item->SendoutBy,
                'SendoutDate' => $item->SendoutDate,
                'DateReceived' => $item->DateReceived,
                'RejectReasonCode' => $item->RejectReasonCode ?? null,
                'IdCompany' => $item->IdCompany,
                'ItemSubGroup' => 'NonBlood',
            ];
        });


        // --- Blood ---
        $bloodData = Queue::laboratoryBloodReceiving($batchCode)->get();

        $groupedBlood = $bloodData->groupBy('ReceivingBatchCode')->map(function ($group) use ($batchCode) {
            // Filter the BloodBatchCode to include only the desired batch code
            $filteredBloodBatchCodes = array_filter(json_decode($group->first()->BloodBatchCode, true), function ($key) use ($batchCode) {
                return $key === $batchCode;
            }, ARRAY_FILTER_USE_KEY);

            // If we have no matching batch code, skip the group
            if (empty($filteredBloodBatchCodes)) {
                return null; // Returning null will remove this group from the collection
            }
            $first = $group->first();
            
            // Determine tube type from the batch code key
            $bloodKey = array_key_first($filteredBloodBatchCodes);
            $tube = $bloodKey ? substr($bloodKey, -1) : null;

            // Define allowed item codes per tube
            $allowedItems = [];
            if ($tube === 'P') {
                $allowedItems = ['LH002', 'LC017'];
            } elseif ($tube === 'Y') {
                $allowedItems = ['LC019', 'LC013'];
            }

            return [
                'ReceivingBatchCode' => $first->ReceivingBatchCode,
                'BloodBatchCode' => $filteredBloodBatchCodes,
                'QueueCode' => $first->QueueCode,
                'QFullName' => $first->QFullName,
                'TubesSent' => json_decode($first->TubesSent, true),
                'Items' => $group->map(function ($item) use ($allowedItems) {
                    // Filter items based on allowed ItemCode
                    if (!in_array($item->ItemCode, $allowedItems)) {
                        return null;
                    }

                    return [
                        'Id' => $item->ReceivingId,
                        'ItemCode' => $item->ItemCode,
                        'ItemDescription' => $item->ItemDescription,
                        'AccessionNo' => $item->AccessionNo,
                        'RejectReasonCode' => $item->RejectReasonCode ?? null,
                    ];
                })->filter()->values(), // remove nulls after filtering
                'DateReceived' => $first->DateReceived,
                'IdCompany' => $first->IdCompany,
                'ItemSubGroup' => 'BLOOD',
            ];
        })->filter()->values(); // remove null groups


        // Merge both
        $tableData = $nonBloodData->merge($groupedBlood);
        // dd($tableData);
        // Company names
        $companyIds = $tableData->pluck('IdCompany')->unique()->values();
        $companyNames = DB::connection('Eros')->table('Company')
            ->whereIn('Id', $companyIds)
            ->pluck('Name')
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'company' => $companyNames->count() > 1 ? $companyNames->toArray() : $companyNames->first(),
                'from' => $batch->FromBU,
                'departure_datetime' => $batch->DepartureDateTime,
                'arrival_datetime' => $batch->ArrivalDateTime,
                'quantity' => $batch->Quantity,
                'status' => $batch->Status,
            ],
            'tableData' => $tableData
        ]);
    }


}
