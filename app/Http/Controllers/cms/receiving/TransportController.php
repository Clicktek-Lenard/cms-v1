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
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Response;
use Picqer\Barcode\BarcodeGeneratorPNG;

class TransportController extends Controller
{
    public function index()
    {
        $received = Queue::specimenReceiving()
            ->whereNull('Receiving.BatchCode')
            ->get()
            // Exclude specific ItemCodes
            ->reject(function ($item) {
                return in_array($item->ItemCode, ['IE001', 'IX020']);
            });
           
        // Split BLOOD from the rest
        $bloodSubGroups = ['CHEMISTRY', 'HEMATOLOGY', 'IMMUNOLOGY'];

        $bloodRaw = $received->filter(function ($item) use ($bloodSubGroups) {
            return in_array($item->ItemSubGroup, $bloodSubGroups);
        });

        $nonBlood = $received->reject(function ($item) use ($bloodSubGroups) {
            return in_array($item->ItemSubGroup, $bloodSubGroups);
        });

        // Process BLOOD group
        $blood = $bloodRaw->groupBy(function ($item) {
            return $item->QueueCode.'-'.$item->ReceivingBatchCode;
        })->flatMap(function ($batchGroup) {
            // All items in this batch
            $items = $batchGroup->map(function ($item) {
                return [
                    'ItemCode' => $item->ItemCode,
                    'ItemDescription' => $item->ItemDescription,
                ];
            });

            // Decode Tubes and TubesSent
            $tubeJson = $batchGroup->first()->Tubes;
            $tubeSentJson = $batchGroup->first()->TubesSent;

            $tubes = json_decode($tubeJson, true) ?? [];
            $tubesSent = json_decode($tubeSentJson, true) ?? [];

            $expanded = collect();

            foreach ($tubes as $color => $count) {
                $sentCount = isset($tubesSent[$color]) ? (int) $tubesSent[$color] : 0;
                $remainingCount = (int) $count - $sentCount;

                // Only add tubes that still need to be sent
                if ($remainingCount > 0) {
                    for ($i = 0; $i < $remainingCount; $i++) {
                        $expanded->push([
                            'QueueCode' => $batchGroup->first()->QueueCode,
                            'ReceivingBatchCode' => $batchGroup->first()->ReceivingBatchCode,
                            'TubeColor' => $color,
                            'Items' => $items,
                            'Patient' => $batchGroup->first()->QFullName,
                            'IdPatient' => $batchGroup->first()->IdPatient,
                            'ReceivedBy' => $batchGroup->first()->ReceivedBy,
                            'DateReceived' => $batchGroup->first()->DateReceived,
                            'LabId' => $batchGroup->first()->LabId
                        ]);
                    }
                }
            }

            return $expanded;
        });

        // 🔍 Debug
        // dd($nonBlood, $blood);

        return view('cms.receiving.transport', ['datas' => $nonBlood, 'blood' => $blood]);
    }


    public function rejectedIndex()
    {
        $rejected = Queue::rejectedSpecimen()->get();
// // 🧠 Check if any have null ItemCode or ItemDescription
// $nullItems = $rejected->filter(function ($item) {
//     return is_null($item->ItemCode) || is_null($item->ItemDescription);
// });

// // Debug output
// if ($nullItems->isNotEmpty()) {
//     dd($nullItems);
// }
        // Define BLOOD subgroups
        $bloodSubGroups = ['CHEMISTRY', 'HEMATOLOGY', 'IMMUNOLOGY'];

        // Split BLOOD from non-BLOOD
        $bloodRaw = $rejected->filter(function ($item) use ($bloodSubGroups) {
            return in_array($item->ItemSubGroup, $bloodSubGroups);
        });

        $nonBlood = $rejected->reject(function ($item) use ($bloodSubGroups) {
            return in_array($item->ItemSubGroup, $bloodSubGroups);
        });

        // BLOOD: group by QueueCode (not ReceivingBatchCode anymore)
        $blood = $bloodRaw
            ->groupBy('QueueCode')
            ->map(function ($queueGroup, $queueCode) {
                $first = $queueGroup->first();

                // Collect items across all ReceivingBatchCodes of this QueueCode
                $items = $queueGroup->map(function ($item) {
                    return [
                        'ItemCode' => $item->ItemCode,
                        'ItemDescription' => $item->ItemDescription,
                        'ReceivingBatchCode' => $item->ReceivingBatchCode, // keep if you still want to see it
                    ];
                })->unique('ItemCode')->values();

                $reasons = $queueGroup
                    ->pluck('Reason')
                    ->filter()
                    ->unique()
                    ->values();

                return [
                    'QueueCode'   => $queueCode,
                    'Patient'     => $first->QFullName,
                    'IdPatient'   => $first->IdPatient,
                    'Items'       => $items,
                    'RejectedBy'  => $first->RejectBy ?? null,
                    'RejectReason' => $reasons,
                    'RejectDate'  => $first->RejectDateTime ?? null,
                ];
            })->values();


        // 🔍 Debug
        // dd($nonBlood, $blood);

        $rejectionData = ErosDB::getRejectionData();

        return view('cms.receiving.rejected', ['blood' => $blood, 'nonBlood' => $nonBlood, 'rejectiondata' => $rejectionData]);
    }


    public function rejectionIndex()
    {
        $received = Queue::specimenReceiving()
            ->whereNull('Receiving.BatchCode')
            ->get()
            // Exclude specific ItemCodes
            ->reject(function ($item) {
                return in_array($item->ItemCode, ['IE001', 'IX020']);
            });

        // Split BLOOD from the rest
        $bloodSubGroups = ['CHEMISTRY', 'HEMATOLOGY', 'IMMUNOLOGY'];

        $bloodRaw = $received->filter(function ($item) use ($bloodSubGroups) {
            return in_array($item->ItemSubGroup, $bloodSubGroups);
        });

        $nonBlood = $received->reject(function ($item) use ($bloodSubGroups) {
            return in_array($item->ItemSubGroup, $bloodSubGroups);
        });

        $blood = $bloodRaw
            ->groupBy('ReceivingBatchCode')
            ->map(function ($batchGroup, $receivingBatchCode) {
                $first = $batchGroup->first();

                // Collect items under this batch
                $items = $batchGroup->map(function ($item) {
                    return [
                        'ItemCode' => $item->ItemCode,
                        'ItemDescription' => $item->ItemDescription,
                    ];
                })->unique('ItemCode')->values();

                // Decode the tube counts only once from the first item
                $tubes = json_decode($first->Tubes, true) ?? [];

                $tubeCounts = collect($tubes)->filter(fn($v) => (int) $v > 0)->map(fn($v) => (int) $v);

                return [
                    'ReceivingBatchCode' => $receivingBatchCode,
                    'QueueCode' => $first->QueueCode,
                    'TubeCounts' => $tubeCounts->filter(fn($v) => $v > 0)->toArray(),
                    'Items' => $items,
                    'Patient' => $first->QFullName,
                    'IdPatient' => $first->IdPatient,
                    'ReceivedBy' => $first->ReceivedBy,
                    'DateReceived' => $first->DateReceived,
                ];
            })->values(); // optional: reset the keys


        // 🔍 Debug
        // dd($nonBlood, $blood);

        $rejectionData = ErosDB::getRejectionData();

        return view('cms.receiving.rejection', ['nonBlood' => $nonBlood, 'blood' => $blood, 'rejectiondata' => $rejectionData]);
    }

    public function edit($id)
    {

    }
    
    public function show($id)
    { 

    }

    public function receive(Request $request) // THIS IS LABORATORY RECEIVING
    {
        $receivingIds = $request->input('receiving_ids', []);

        if (empty($receivingIds)) {
            return response()->json(['success' => false, 'message' => 'No specimen selected']);
        }

        foreach ($receivingIds as $id) {
            DB::connection('CMS')->table('Receiving')
                ->where('Id', $id)
                ->update([
                    'SendoutReceivedDate' => now(),
                    'SendoutReceivedBy' => Auth::user()->username,
                    'SendOutStatus' => 'Specimen Received',
                ]);
        }

        return response()->json(['success' => true, 'message' => 'Specimens successfully received.']);
    }

    public function receiveSpecimen(Request $request) // THIS IS RE-RECEIVING SPECIMEN
    {
        $data = json_decode($request->input('receiveSpecimen'), true);

        if (!$data || empty($data)) {
            return back()->with('error', 'No data to receive.');
        }

            foreach ($data as $row) {
            if ($row['ItemSubGroup'] != 'BLOOD') {
                // 🔹 Non-BLOOD 
                $existingRow = DB::connection('CMS')
                    ->table('Receiving')
                    ->where('Id', $row['ReceivingId'])
                    ->first();

                if ($existingRow) {
                        // COPY/MOVE EXISTING ROW TO REJECTED
                    DB::connection('CMS')->table('Rejected')->insert([
                        'Id'                    => $existingRow->Id,
                        'IdQueue'               => $existingRow->IdQueue,
                        'QueueCode'             => $existingRow->QueueCode,
                        'IdBUFrom'              => $existingRow->IdBUFrom,
                        'IdBUTo'                => $existingRow->IdBUTo,
                        'IdTransaction'         => $existingRow->IdTransaction,
                        'ItemCode'              => $existingRow->ItemCode,
                        'PackageCode'           => $existingRow->PackageCode,
                        'DateReceived'          => $existingRow->DateReceived,
                        'ReceivedBy'            => $existingRow->ReceivedBy,
                        'Notes'                 => $existingRow->Notes,
                        'Tubes'                 => $existingRow->Tubes,
                        'TubesSent'             => $existingRow->TubesSent,
                        'TubesReject'           => $existingRow->TubesReject,
                        'ReceivingBatchCode'    => $existingRow->ReceivingBatchCode,
                        'Status'                => $existingRow->Status,
                        'SendoutDate'           => $existingRow->SendoutDate,
                        'SendoutBy'             => $existingRow->SendoutBy,
                        'SendoutReceivedDate'   => $existingRow->SendoutReceivedDate,
                        'SendoutReceivedBy'     => $existingRow->SendoutReceivedBy,
                        'SendoutStatus'         => $existingRow->SendoutStatus,
                        'BatchCode'             => $existingRow->BatchCode,
                        'BloodBatchCode'        => $existingRow->BloodBatchCode,
                        'RejectBy'              => $existingRow->RejectBy,
                        'Reason'                => $existingRow->Reason,
                        'RejectDateTime'        => $existingRow->RejectDateTime
                    ]);
                        
                    // DELETE EXISTING ROW ON RECEIVING
                    DB::connection('CMS')->table('Receiving')
                        ->where('Id', $row['ReceivingId'])
                        ->delete();
                }

                //WRITE NEW RECEIVE
                DB::connection('CMS')->table('Receiving')->insert([
                    'IdQueue'      => $existingRow->IdQueue,
                    'QueueCode'    => $existingRow->QueueCode,
                    'IdBUFrom'     => $existingRow->IdBUFrom,
                    'IdBUTo'       => $existingRow->IdBUTo,
                    'IdTransaction'=> $existingRow->IdTransaction,
                    'ItemCode'     => $existingRow->ItemCode,
                    'PackageCode'  => $existingRow->PackageCode,
                    'DateReceived' => now(),
                    'ReceivedBy'   => Auth::user()->username,
                    'Status'       => 'Specimen Received',
                    'Notes'        => $existingRow->Notes,
                ]);

                DB::connection('CMS')->table('AccessionNo')
                    ->where('Id', $row['Id'])
                    ->update([
                        'Status' => 311,
                        'ReceivedBU' => session('userClinicCode'),
                        'ExamDate' => now()
                    ]);

            } else {
                // 🔹 BLOOD 
                $itemCodes = array_map('trim', explode(',', $row['ItemCode']));

                $existingRows = DB::connection('CMS')
                    ->table('Receiving')
                    ->where('QueueCode', $row['Code'])
                    ->whereIn('ItemCode', $itemCodes)
                    ->get();
                
                    // Fetch all existing batch codes for this QueueCode
                $existingBatches = Receiving::where('QueueCode', $row['Code'])
                    ->whereNotNull('ReceivingBatchCode')
                    ->pluck('ReceivingBatchCode')
                    ->toArray();

                // Find the highest letter suffix used so far
                $lastLetter = null;
                foreach ($existingBatches as $batch) {
                    $suffix = substr($batch, strlen($row['Code']));
                    if ($suffix && ctype_alpha($suffix)) {
                        if ($lastLetter === null || strcmp($suffix, $lastLetter) > 0) {
                            $lastLetter = $suffix;
                        }
                    }
                }

                // Calculate next letter
                if ($lastLetter === null) {
                    $nextLetter = 'A';
                } else {
                    // If last letter is 'Z', you can add logic to extend ('AA', 'AB') here if needed
                    $nextLetter = chr(ord($lastLetter) + 1);
                }

                // Compose batch code
                $batchCode = $row['Code'] . $nextLetter;

                if ($existingRows->isNotEmpty()) {
                    foreach ($existingRows as $existing) {
                        DB::connection('CMS')->table('Rejected')->insert([
                            'Id'                    => $existing->Id,
                            'IdQueue'               => $existing->IdQueue,
                            'QueueCode'             => $existing->QueueCode,
                            'IdBUFrom'              => $existing->IdBUFrom,
                            'IdBUTo'                => $existing->IdBUTo,
                            'IdTransaction'         => $existing->IdTransaction,
                            'ItemCode'              => $existing->ItemCode,
                            'PackageCode'           => $existing->PackageCode,
                            'DateReceived'          => $existing->DateReceived,
                            'ReceivedBy'            => $existing->ReceivedBy,
                            'Notes'                 => $existing->Notes,
                            'Tubes'                 => $existing->Tubes,
                            'TubesSent'             => $existing->TubesSent,
                            'TubesReject'           => $existing->TubesReject,
                            'ReceivingBatchCode'    => $existing->ReceivingBatchCode,
                            'Status'                => $existing->Status,
                            'SendoutDate'           => $existing->SendoutDate,
                            'SendoutBy'             => $existing->SendoutBy,
                            'SendoutReceivedDate'   => $existing->SendoutReceivedDate,
                            'SendoutReceivedBy'     => $existing->SendoutReceivedBy,
                            'SendoutStatus'         => $existing->SendoutStatus,
                            'BatchCode'             => $existing->BatchCode,
                            'BloodBatchCode'        => $existing->BloodBatchCode,
                            'RejectBy'              => $existing->RejectBy,
                            'Reason'                => $existing->Reason,
                            'RejectDateTime'        => $existing->RejectDateTime,
                        ]);

                        // Delete the original row
                        DB::connection('CMS')->table('Receiving')
                            ->where('Id', $existing->Id)
                            ->delete();
                    }

                    foreach ($existingRows as $existing) {
                        DB::connection('CMS')->table('Receiving')->insert([
                            'IdQueue'       => $existing->IdQueue,
                            'QueueCode'     => $existing->QueueCode,
                            'IdBUFrom'      => $existing->IdBUFrom,
                            'IdBUTo'        => $existing->IdBUTo,
                            'IdTransaction' => $existing->IdTransaction,
                            'ItemCode'      => $existing->ItemCode,
                            'PackageCode'   => $existing->PackageCode,
                            'DateReceived'  => now(),
                            'ReceivedBy'    => Auth::user()->username,
                            'Status'        => 'Specimen Received',
                            'Notes'         => $existing->Notes,
                            'Tubes'         => json_encode($row['Tubes']),
                            'TubesSent'     => json_encode(['purple' => 0, 'yellow' => 0, 'blue' => 0, 'red' => 0, 'gray' => 0]),
                            'TubesReject'   => json_encode(['purple' => 0, 'yellow' => 0, 'blue' => 0, 'red' => 0, 'gray' => 0]),
                            'ReceivingBatchCode' => $batchCode
                        ]);

                        DB::connection('CMS')->table('AccessionNo')
                            ->where('QueueCode', $existing->QueueCode)
                            ->where('ItemCode', $existing->ItemCode)
                            ->update(['Status' => 311]);
                    }
                }
            }

        }

        return back()->with('success', 'Specimens successfully marked as Specimen Received.');
    }

    public function rejectSpecimen(Request $request)
    {
        $data = json_decode($request->input('rejectedSpecimen'), true);

        if (!$data || empty($data)) {
            return back()->with('error', 'No data to reject.');
        }

        foreach ($data as $row) {
            if (empty($row['ReceivingBatchCode'])) {
                // 🔹 Non-BLOOD (ReceivingId based)
                DB::connection('CMS')->table('Receiving')
                    ->where('Id', $row['ReceivingId'])
                    ->update([
                        'RejectDateTime' => now(),
                        'Reason' => $row['RejectionReason'],
                        'RejectBy' => Auth::user()->username,
                        'Status' => ($row['RejectionReason'] === 'R0015') ? 'Waived' : 'Rejected',
                    ]);
                
                DB::connection('CMS')->table('AccessionNo')
                    ->where('IdQueue', $row['Id'])
                    ->where('ItemCode', $row['ItemCode'])
                    ->update(['Status' => ($row['RejectionReason'] === 'R0015') ? 888 : 877]);

            } else {
                // 🔹 BLOOD (ReceivingBatchCode based)
                $batchCode = $row['ReceivingBatchCode'];
                $tubeColor = strtolower(explode(':', $row['ItemCode'])[0]);  // e.g. "purple"
                $rejectedCount = (int) trim(explode(':', $row['ItemCode'])[1]);

                // Get all rows for this batch
                $rows = DB::connection('CMS')->table('Receiving')
                    ->where('ReceivingBatchCode', $batchCode)
                    ->get();

                foreach ($rows as $dbRow) {
                    $tubes = json_decode($dbRow->Tubes, true) ?: [];
                    $tubesReject = json_decode($dbRow->TubesReject, true) ?: [];

                    // Adjust tube counts
                    $tubes[$tubeColor] = max(0, ((int)$tubes[$tubeColor]) - $rejectedCount);
                    $tubesReject[$tubeColor] = ((int)($tubesReject[$tubeColor] ?? 0)) + $rejectedCount;

                    // If this row's ItemCode is included in rejection → apply reject info
                    if (strpos($row['ItemDescription'], $dbRow->ItemCode) !== false) {
                        DB::connection('CMS')->table('Receiving')
                            ->where('Id', $dbRow->Id)
                            ->update([
                                'Tubes' => json_encode($tubes),
                                'TubesReject' => json_encode($tubesReject),
                                'RejectDateTime' => now(),
                                'Reason' => $row['RejectionReason'],
                                'RejectBy' => Auth::user()->username,
                                'Status' => 'Rejected',
                            ]);
                        
                        // 🔹 Update AccessionNo
                        DB::connection('CMS')->table('AccessionNo')
                            ->where('IdTransaction', $dbRow->IdTransaction)
                            ->where('ItemCode', $dbRow->ItemCode)
                            ->update(['Status' => 877]);
                    } else {
                        // Other rows: only adjust tubes
                        DB::connection('CMS')->table('Receiving')
                            ->where('Id', $dbRow->Id)
                            ->update([
                                'Tubes' => json_encode($tubes),
                                'TubesReject' => json_encode($tubesReject),
                            ]);
                    }
                }
            }
        }

        // NEXT DITO NAMAN YUNG ACCESSION UPDATING STATUS 877

        return back()->with('success', 'Specimens successfully marked as Rejected.');
    }


    public function generateWordDoc(Request $request)
    {
        $data = $request->input('data');

        if (!$data || empty($data)) {
            return back()->with('error', 'No data to generate Word document.');
        }

        // Group data: BLOOD separately, others by ItemDescription
        $groupedData = collect($data)->groupBy(function ($item) {
            $bloodSubGroups = ['BLOOD'];

            return in_array($item['ItemSubGroup'], $bloodSubGroups) ? 'BLOOD' : $item['ItemDescription'];
        });

        // Group BLOOD by Code (one row per patient/transaction)
        $groupedData->transform(function ($items, $key) {
            return $key === 'BLOOD' ? collect($items)->groupBy('ReceivingBatchCode') : $items;
        });

        // Generate Batch Prefix (date-based)
        $dateCode = now()->format('Ymd');
        $myDBId = Controller::getMyDBID();
        $batchPrefix = "DRE" . $myDBId . $dateCode;

        // ---- compute global lastNumber ONCE (Transport + BloodBatchCode) ----
        $lastNumber = 0;

        // 1) existing BatchCode entries in Transport
        $transportCodes = DB::connection('CMS')->table('Transport')
            ->where('BatchCode', 'like', $batchPrefix . '%')
            ->pluck('BatchCode')
            ->filter()
            ->values()
            ->all();

        foreach ($transportCodes as $code) {
            if (preg_match('/^' . preg_quote($batchPrefix, '/') . '(\d{4})([A-Z]?)$/', $code, $m)) {
                $lastNumber = max($lastNumber, intval($m[1]));
            }
        }

        // 2) codes inside BloodBatchCode JSON arrays (Receiving only)
        $bloodJsons = DB::connection('CMS')->table('Receiving')
            ->where('BloodBatchCode', 'like', '%' . $batchPrefix . '%')
            ->pluck('BloodBatchCode')
            ->filter()
            ->values()
            ->all();

        foreach ($bloodJsons as $json) {
            $codes = json_decode($json, true);
            if (!is_array($codes)) continue;
            foreach ($codes as $c) {
                if (preg_match('/^' . preg_quote($batchPrefix, '/') . '(\d{4})[A-Z]$/', $c, $m)) {
                    $lastNumber = max($lastNumber, intval($m[1]));
                }
            }
        }

        if (! $groupedData->has('BLOOD')) {
            // Non-blood items → single BatchCode
            $nextBatchNumber = $lastNumber + 1;
            $batchCode = $batchPrefix . str_pad($nextBatchNumber, 4, '0', STR_PAD_LEFT);

            DB::connection('CMS')->table('Transport')->insert([
                'BatchCode' => $batchCode,
                'Quantity' => count($data),
                'TransportDate' => now(),
                'FromBU' => session('userClinicCode'),
                'ToBU' => '',
                'Status' => 'In Transit',
                'PreparedBy' => Auth::user()->username,
                'DepartureDateTime' => now(),
                'ArrivalDateTime' => ''
            ]);

            foreach ($data as $row) {
                DB::connection('CMS')->table('Receiving')
                    ->where('Id', $row['ReceivingId'])->update([
                        'SendoutDate' => now(),
                        'SendoutBy' => Auth::user()->username,
                        'SendoutStatus' => 'Send Out',
                        'BatchCode' => $batchCode
                    ]);
            }
        } else {
            // Blood items → one BatchCode per tube type for this send
            $nextSeq = $lastNumber;

            // Step 1: build tubeType => assignedCode for THIS send
            $tubeAssignments = [];     
            $transportQuantities = []; 

            foreach ($groupedData['BLOOD'] as $receivingBatchCode => $items) {
                foreach ($items as $dataRow) {
                    $tubeTypeRaw = $dataRow['Tubes'] ?? '';
                    $tubeType = strtolower(trim($tubeTypeRaw));
                    if ($tubeType === '') continue;

                    if (!isset($tubeAssignments[$tubeType])) {
                        $nextSeq++;
                        $suffix = strtoupper(substr($tubeType, 0, 1)) ?: 'X';
                        $assigned = $batchPrefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT) . $suffix;
                        $tubeAssignments[$tubeType] = $assigned;
                        $transportQuantities[$assigned] = 0;
                    }
                }
            }

            // Step 2: update each Receiving row
            foreach ($groupedData['BLOOD'] as $receivingBatchCode => $items) {
                foreach ($items as $dataRow) {
                    $rowBatchCode = $dataRow['ReceivingBatchCode'];
                    $tubeTypeRaw = $dataRow['Tubes'] ?? '';
                    $tubeType = strtolower(trim($tubeTypeRaw));
                    if ($tubeType === '') continue;

                    $assignedCode = $tubeAssignments[$tubeType];

                    $receiving = DB::connection('CMS')->table('Receiving')
                        ->where('ReceivingBatchCode', $rowBatchCode)
                        ->first();

                    if (! $receiving) continue;

                    $tubes = json_decode($receiving->Tubes, true);
                    $tubesSent = json_decode($receiving->TubesSent, true);
                    $bloodBatchCodes = json_decode($receiving->BloodBatchCode, true);

                    if (!is_array($tubes)) $tubes = [];
                    if (!is_array($tubesSent)) {
                        $tubesSent = [
                            "purple" => "0", "yellow" => "0", "blue" => "0",
                            "red" => "0", "green" => "0"
                        ];
                    }

                    // Normalize legacy BloodBatchCode formats
                    if (!is_array($bloodBatchCodes)) {
                        $bloodBatchCodes = [];
                    } else {
                        $is_list = array_values($bloodBatchCodes) === $bloodBatchCodes;
                        if ($is_list) {
                            $tmp = [];
                            foreach ($bloodBatchCodes as $c) {
                                if (!isset($tmp[$c])) {
                                    $tmp[$c] = "1";
                                } else {
                                    $tmp[$c] = (string)(((int)$tmp[$c]) + 1);
                                }
                            }
                            $bloodBatchCodes = $tmp;
                        }
                    }

                    // increment sent count
                    $tubesSent[$tubeType] = (string)(((int)($tubesSent[$tubeType] ?? 0)) + 1);

                    // track under this batch code
                    if (!isset($bloodBatchCodes[$assignedCode])) {
                        $bloodBatchCodes[$assignedCode] = "0";
                    }
                    $bloodBatchCodes[$assignedCode] = (string)(((int)$bloodBatchCodes[$assignedCode]) + 1);

                    // accumulate transport totals
                    if (!isset($transportQuantities[$assignedCode])) {
                        $transportQuantities[$assignedCode] = 0;
                    }
                    $transportQuantities[$assignedCode]++;

                    // always update TubesSent
                    DB::connection('CMS')->table('Receiving')
                        ->where('ReceivingBatchCode', $rowBatchCode)
                        ->update([
                            'TubesSent' => json_encode($tubesSent),
                        ]);

                    // only update BloodBatchCode if not rejected
                    DB::connection('CMS')->table('Receiving')
                        ->where('ReceivingBatchCode', $rowBatchCode)
                        ->where('Status', '!=', 'Rejected')
                        ->update([
                            'BloodBatchCode' => json_encode($bloodBatchCodes),
                        ]);
                }
            }

            // Step 3: insert one Transport row per batch code
            foreach ($tubeAssignments as $tubeType => $assignedCode) {
                DB::connection('CMS')->table('Transport')->insert([
                    'BatchCode' => $assignedCode,
                    'Quantity' => (string)($transportQuantities[$assignedCode] ?? 0),
                    'TransportDate' => now(),
                    'FromBU' => session('userClinicCode'),
                    'ToBU' => '',
                    'Status' => 'In Transit',
                    'PreparedBy' => Auth::user()->username,
                    'DepartureDateTime' => now(),
                    'ArrivalDateTime' => ''
                ]);
            }
            
            $batchCode = reset($tubeAssignments);
        }

        // dd($batchCode);
        // Generate batch barcode
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcodeData = $generator->getBarcode($batchCode, $generator::TYPE_CODE_128);
        $barcodeFile = tempnam(sys_get_temp_dir(), 'barcode_') . '.png';
        file_put_contents($barcodeFile, $barcodeData);

        // Process each group
        foreach ($groupedData as $groupName => $group) {

            // Select template
            if ($groupName === 'BLOOD') {
                $templatePath = public_path('uploads/QueueDisplay/templateBlood.docx');
            } else {
                $numGroups = count($groupedData);
                switch ($numGroups) {
                    case 1: $templatePath = public_path('uploads/QueueDisplay/templatetest1.docx'); break;
                    case 2: $templatePath = public_path('uploads/QueueDisplay/templatetest2.docx'); break;
                    case 3: $templatePath = public_path('uploads/QueueDisplay/templatetest3.docx'); break;
                    case 4: $templatePath = public_path('uploads/QueueDisplay/templatetest4.docx'); break;
                    case 5: $templatePath = public_path('uploads/QueueDisplay/templatetest5.docx'); break;
                    case 6: $templatePath = public_path('uploads/QueueDisplay/templatetest6.docx'); break;
                    default: $templatePath = public_path('uploads/QueueDisplay/templatetest7.docx'); break;
                }
            }

            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

            // Common placeholders
            $templateProcessor->setImageValue('barcode', [
                'path' => $barcodeFile,
                'width' => 200,
                'height' => 50,
                'ratio' => true
            ]);
            $templateProcessor->setValue('batchcode', $batchCode);

            $firstRow = reset($data);
            $labId = $firstRow['LabId'] ?? '';
            $labName = '';
            if ($labId) {
                $company = DB::connection('Eros')->table('Company')
                    ->where('Id', $labId)
                    ->first();

                if ($company) {
                    $labName = $company->Name;
                }
            }

            $templateProcessor->setValue('location', htmlspecialchars($labName ?? '', ENT_QUOTES, 'UTF-8'));

            $templateProcessor->setValue('generatedTime', now()->format('Y-m-d H:i'));

            if ($groupName === 'BLOOD') {
                // Count total rows = number of ReceivingBatchCode groups
                $totalRows = count($group);
                $templateProcessor->cloneRow('code', $totalRows);

                $rowIndex = 1;

                // ✅ Detect tube type for this send (always the same)
                $firstRow = $group->first()->first();
                $sentTube = strtolower($firstRow['Tubes'] ?? '');
                $tubeHeaderMap = [
                    'purple' => 'EDTA',
                    'yellow' => 'Yellow',
                    'blue'   => 'Blue',
                    'red'    => 'Red',
                ];
                $tubeHeader = $tubeHeaderMap[$sentTube] ?? ucfirst($sentTube);

                // Set the header placeholder once
                $templateProcessor->setValue('tubeHeader', $tubeHeader);

                $totalCount = 0;
                $rowIndex = 1;
                foreach ($group as $receivingBatchCode => $tests) {
                    $firstRow = $tests->first();
                    $fullName = $firstRow['QFullName'] ?? '';
                    $dateReceived = $firstRow['DateReceived'] ?? '';
                    $receivedBy = $firstRow['ReceivedBy'] ?? '';

                    // Count only this tube type
                    $count = 0;
                    foreach ($tests as $row) {
                        if (strtolower($row['Tubes'] ?? '') === $sentTube) {
                            $count++;
                        }
                    }
                    $totalCount += $count;

                    // Barcode for AccNo
                    $rowBarcodeValue = $firstRow['Code'];
                    $rowBarcodeData = $generator->getBarcode($rowBarcodeValue, $generator::TYPE_CODE_128);
                    $rowBarcodeFile = tempnam(sys_get_temp_dir(), "barcode_row_{$rowIndex}_") . '.png';
                    file_put_contents($rowBarcodeFile, $rowBarcodeData);

                    $templateProcessor->setImageValue("code#{$rowIndex}", [
                        'path' => $rowBarcodeFile,
                        'width' => 150,
                        'height' => 40,
                        'ratio' => true
                    ]);
                    // ------------------------

                    // Fill row placeholders
                    $templateProcessor->setValue("fullname#{$rowIndex}", htmlspecialchars($fullName ?? '', ENT_QUOTES, 'UTF-8'));
                    $templateProcessor->setValue("datereceived#{$rowIndex}", $dateReceived);
                    $templateProcessor->setValue("receivedby#{$rowIndex}", $receivedBy);
                    $templateProcessor->setValue("tubeCount#{$rowIndex}", $count);

                    DB::connection('CMS')->table('TransmittalOrder')->insert([
                        'BatchCode'      => $batchCode,
                        'QueueCode'      => $firstRow['Code'] ?? '',
                        'PatientName' => htmlspecialchars($fullName ?? '', ENT_QUOTES, 'UTF-8'),
                        'ItemDescription'=> null,
                        'Tubes'          => $count,
                        'ReceivedTime'   => $dateReceived,
                        'ReceivedBy'     => $receivedBy,
                        'rowIndex'       => $rowIndex,
                    ]);
                    
                    $rowIndex++;
                }

                // total
                $templateProcessor->setValue('tubeTotal', $totalCount);
            } else {
                $maxTables = min($numGroups, 7);        
                $groupIndex = 1;

                foreach ($groupedData as $itemDescription => $group) {
                    if ($groupIndex > $maxTables) break;

                    // Set table title
                    $templateProcessor->setValue("itemdescription{$groupIndex}_title", $itemDescription);

                    // Clone rows for this group
                    $templateProcessor->cloneRow("code{$groupIndex}", count($group));

                    foreach ($group as $index => $row) {
                        $i = $index + 1;

                        // Generate barcode for each row
                        $rowBarcodeValue = $row['Code']; // queue code only
                        $rowBarcodeData = $generator->getBarcode($rowBarcodeValue, $generator::TYPE_CODE_128);
                        $rowBarcodeFile = tempnam(sys_get_temp_dir(), "barcode_row_{$groupIndex}_{$i}_") . '.png';
                        file_put_contents($rowBarcodeFile, $rowBarcodeData);

                        $templateProcessor->setImageValue("code{$groupIndex}#{$i}", [
                            'path' => $rowBarcodeFile,
                            'width' => 150,
                            'height' => 40,
                            'ratio' => true
                        ]);

                        // ✅ Override item description if ItemCode = LH002
                        $itemDesc = $row['ItemDescription'] ?? '';
                        if (($row['ItemCode'] ?? '') === 'LH002') {
                            $itemDesc = 'CBC / QUANTITATIVE PLATELET';
                        }

                        $templateProcessor->setValue("fullname{$groupIndex}#{$i}", htmlspecialchars($row['QFullName'] ?? $row['FullName'] ?? '', ENT_QUOTES, 'UTF-8'));
                        $templateProcessor->setValue("itemcode{$groupIndex}#{$i}", $row['ItemCode'] ?? '');
                        $templateProcessor->setValue("itemdescription{$groupIndex}#{$i}", $itemDesc);
                        $templateProcessor->setValue("datereceived{$groupIndex}#{$i}", $row['DateReceived'] ?? '');
                        $templateProcessor->setValue("receivedby{$groupIndex}#{$i}", $row['ReceivedBy'] ?? '');

                        // Insert NON-BLOOD row (with ItemDescription)
                        DB::connection('CMS')->table('TransmittalOrder')->insert([
                            'BatchCode'      => $batchCode,
                            'QueueCode'      => $row['Code'] ?? '',
                            'PatientName' => htmlspecialchars($row['QFullName'] ?? $row['FullName'] ?? '', ENT_QUOTES, 'UTF-8'),
                            'ItemDescription'=> $itemDesc,
                            'Tubes'          => null,
                            'ReceivedTime'   => $row['DateReceived'] ?? null,
                            'ReceivedBy'     => $row['ReceivedBy'] ?? '',
                            'rowIndex'       => $i,
                        ]);
                    }

                    // ✅ Set quantity per group
                    $templateProcessor->setValue("quantity{$groupIndex}", count($group));

                    $groupIndex++;
                }
            }


            // Determine filename based on group
            $filename = $groupName === 'BLOOD' 
                ? 'blood_report_' . now()->format('Ymd_His') . '.docx'
                : 'specimen_report_' . now()->format('Ymd_His') . '.docx';

            // Save to a temp file for download
            $tempFile = tempnam(sys_get_temp_dir(), 'word_') . '.docx';
            $templateProcessor->saveAs($tempFile);

            // ✅ Also save a permanent copy to QueueDisplay/TRANSMITTALS with BatchCode as name
            $localFilename = $batchCode . '.docx';
            $savePath = public_path('uploads/QueueDisplay/TRANSMITTALS/' . $localFilename);
            if (!file_exists(dirname($savePath))) {
                mkdir(dirname($savePath), 0777, true);
            }
            copy($tempFile, $savePath);

            // Return the popup download with report-style filename
            return response()->download($tempFile, $filename)
                ->deleteFileAfterSend(true);
        }
    }



        // // Insert into Transport table
        // DB::connection('CMS')->table('Transport')->insert([
        //     'BatchCode' => $batchCode,
        //     'Quantity' => count($data),
        //     'TransportDate' => now(),
        //     'FromBU' => session('userClinicCode'),
        //     'ToBU' => '',
        //     'Status' => 'In Transit',
        //     'PreparedBy' => Auth::user()->username,
        //     'DepartureDateTime' => now(),
        //     'ArrivalDateTime' => ''
        // ]);

        // // Update Receiving table
        // foreach ($data as $row) {
        //     DB::connection('CMS')->table('Receiving')
        //         ->where('Id', $row['ReceivingId'])->update([
        //             'SendoutDate' => now(),
        //             'SendoutBy' => Auth::user()->username,
        //             'SendoutStatus' => 'Send Out',
        //             'BatchCode' => $batchCode
        //         ]);
        // }

        
        // $itemDescription = $row['ItemDescription'] ?? '';
        // if ($row['ItemCode'] === 'LH002') {
        //     $itemDescription = 'CBC / QUANTITATIVE PLATELET';
        // }

        // // Remove unused tables by clearing their placeholders
        // for ($t = $groupIndex; $t <= $maxTables; $t++) {
        //     $templateProcessor->setValue("itemdescription{$t}_title", '');
        //     $templateProcessor->setValue("code{$t}", '');
        //     $templateProcessor->setValue("fullname{$t}", '');
        //     $templateProcessor->setValue("itemcode{$t}", '');
        //     $templateProcessor->setValue("itemdescription{$t}", '');
        //     $templateProcessor->setValue("datereceived{$t}", '');
        //     $templateProcessor->setValue("receivedby{$t}", '');
        // }

                // Loop through grouped data
        // $maxTables = min($numGroups, 7);        
        // $groupIndex = 1;

        // foreach ($groupedData as $itemDescription => $group) {
        //     if ($groupIndex > $maxTables) break;

        //     // Set table title
        //     $templateProcessor->setValue("itemdescription{$groupIndex}_title", $itemDescription);

        //     // Clone rows for this group
        //     $templateProcessor->cloneRow("code{$groupIndex}", count($group));

        //     foreach ($group as $index => $row) {
        //         $i = $index + 1;

        //         // Generate barcode for each row
        //         // $rowBarcodeValue = $row['Code'] . '-' . ($row['ItemCode'] ?? '');  // THIS IS WITH ITEM CODE (QUEUE CODE - ITEMCODE)
        //         $rowBarcodeValue = $row['Code']; // ITO WALA QUEUE CODE ONLY
        //         $rowBarcodeData = $generator->getBarcode($rowBarcodeValue, $generator::TYPE_CODE_128);
        //         $rowBarcodeFile = tempnam(sys_get_temp_dir(), "barcode_row_{$groupIndex}_{$i}_") . '.png';
        //         file_put_contents($rowBarcodeFile, $rowBarcodeData);

        //         $templateProcessor->setImageValue("code{$groupIndex}#{$i}", [
        //             'path' => $rowBarcodeFile,
        //             'width' => 150,
        //             'height' => 40,
        //             'ratio' => true
        //         ]);

        //         // ✅ Override item description if ItemCode = LH002
        //         $itemDescription = $row['ItemDescription'] ?? '';
        //         if (($row['ItemCode'] ?? '') === 'LH002') {
        //             $itemDescription = 'CBC / QUANTITATIVE PLATELET';
        //         }

        //         $templateProcessor->setValue("fullname{$groupIndex}#{$i}", $row['QFullName'] ?? $row['FullName']);
        //         $templateProcessor->setValue("itemcode{$groupIndex}#{$i}", $row['ItemCode'] ?? '');
        //         $templateProcessor->setValue("itemdescription{$groupIndex}#{$i}", $itemDescription);
        //         $templateProcessor->setValue("datereceived{$groupIndex}#{$i}", $row['DateReceived'] ?? '');
        //         $templateProcessor->setValue("receivedby{$groupIndex}#{$i}", $row['ReceivedBy'] ?? '');
        //     }

        //     $groupIndex++;
        // }

        // TESTING DATA FOR BLOOD
        // Generate 100 test blood entries
        // $data = [];
        // for ($i = 1; $i <= 100; $i++) {
        //     $data[] = [
        //         'ReceivingId'     => $i,
        //         'Code'            => 'BLOOD' . str_pad($i, 4, '0', STR_PAD_LEFT),
        //         'QFullName'       => 'Patient ' . $i,
        //         'FullName'        => 'Patient ' . $i,
        //         'ItemCode'        => 'LH002',
        //         'ItemDescription' => 'Blood Test - CHEMISTRY',
        //         'DateReceived'    => now()->format('Y-m-d H:i'),
        //         'ReceivedBy'      => 'Tester',
        //         'ItemSubGroup'    => 'CHEMISTRY',
        //     ];
        // }

        // // TESTING DATA FOR SPECIMEN
        // // Generate 50 fecalysis entries
        // $fecalysisData = [];
        // for ($i = 1; $i <= 10; $i++) {
        //     $fecalysisData[] = [
        //         'ReceivingId'     => $i,
        //         'Code'            => 'FECA' . str_pad($i, 4, '0', STR_PAD_LEFT),
        //         'QFullName'       => 'Patient ' . $i,
        //         'FullName'        => 'Patient ' . $i,
        //         'ItemCode'        => 'FE001',
        //         'ItemDescription' => 'Fecalysis Test',
        //         'DateReceived'    => now()->format('Y-m-d H:i'),
        //         'ReceivedBy'      => 'Tester',
        //         'ItemSubGroup'    => 'FECALYSIS',
        //     ];
        // }

        // // Generate 50 urinalysis entries
        // $urinalysisData = [];
        // for ($i = 1; $i <= 10; $i++) {
        //     $urinalysisData[] = [
        //         'ReceivingId'     => $i,
        //         'Code'            => 'FECA' . str_pad($i, 4, '0', STR_PAD_LEFT),
        //         'QFullName'       => 'Patient ' . $i,
        //         'FullName'        => 'Patient ' . $i,
        //         'ItemCode'        => 'FE001',
        //         'ItemDescription' => 'Urinalysis Test',
        //         'DateReceived'    => now()->format('Y-m-d H:i'),
        //         'ReceivedBy'      => 'Tester', 
        //         'ItemSubGroup'    => 'URINALYSIS',
        //     ];
        // }

        // // Merge for testing multiple specimen types together
        // $data = array_merge($fecalysisData, $urinalysisData);
}
