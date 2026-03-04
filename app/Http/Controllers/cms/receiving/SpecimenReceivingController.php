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


class SpecimenReceivingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    // public function index(Request $request)
    // {
    //     $queue = Queue::todaysQueue()->get(array('CMS.Queue.Id','CMS.Queue.Code','CMS.Queue.QFullName','CMS.QueueStatus.Name as QueueStatus','CMS.Queue.InputBy','CMS.Queue.Notes'));
    //     return view('cms.specimenReceiving', ['queue' => $queue]);	
    // }
    public function indexSpecimenReceiving(Request $request)
    {
        $station = ['MICROSCOPY', 'MICROBIOLOGY'];
        $statuses = ['completed', 'in_progress', 'waiting', 'startQueue', 'on_hold', 'resume_queue', 'next_room']; // Replace with the desired status
    
        $queue = Kiosk::getQueuePaid($station, $statuses, 'onSite');

        $received = Queue::specimenReceiving()
            ->where('Type', '=', 'LABORATORY')
            ->whereNull('BatchCode')
	        ->limit(50)
            ->get();

        return view('cms.receiving.specimenReceiving', ['queue' => $queue, 'received' => $received]);	
    }

    public function indexBloodExtraction(Request $request)
    {
        $station = ['HEMATOLOGY', 'CHEMISTRY', 'IMMUNOLOGY'];
        $statuses = ['completed', 'in_progress', 'waiting', 'startQueue', 'on_hold', 'resume_queue', 'next_room']; // Replace with the desired status
    
        $queue = Kiosk::getQueuePaid($station, $statuses, 'onSite');

        $received = Queue::specimenReceiving()
            ->where('Type', '=', 'LABORATORY')
            ->whereNull('BatchCode')
	        ->limit(50)
            ->get();

        return view('cms.receiving.bloodExtraction', ['queue' => $queue, 'received' => $received]);	
    }

    public function indexImaging(Request $request)
    {
        $station = ['XRAY', 'ECG'];
        $statuses = ['completed', 'in_progress', 'waiting', 'startQueue', 'on_hold', 'resume_queue', 'next_room']; // Replace with the desired status
    
        $queue = Kiosk::getQueuePaid($station, $statuses, 'onSite');

        $received = Queue::specimenReceiving()
            ->where('Type', '=', 'IMAGING')
	        ->limit(50)
            ->get();

        return view('cms.receiving.imaging', ['queue' => $queue, 'received' => $received]);	
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {

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
    public function edit(Request $request, $id)
    {
        $subgroup = explode(',', $request->query('subgroup'));      
        $queue = DB::connection('CMS')->table('Queue')->where('Id', $id)->first();
        $items = DB::connection('CMS')
            ->table('AccessionNo as Acc')
            ->leftJoin('QueueStatus as QueueStat', 'Acc.Status', '=', 'QueueStat.Id')
            ->leftJoin('Transactions as Trans', 'Acc.IdTransaction', '=', 'Trans.Id')
            ->leftJoin('Eros.ItemMaster as Item', 'Acc.ItemCode', '=', 'Item.Code') // <-- cross-database join
            ->where('Acc.IdQueue', $id)
            // ->where('Acc.ItemGroup', $department)
            ->where('Acc.Status', 300)
            ->whereIn('Item.SubGroup', $subgroup)
            ->select(
                'Acc.IdTransaction', 'Acc.IdQueue', 'Acc.ItemCode', 'Acc.ItemDescription',
                'QueueStat.Name as StatusName',
                'Trans.Id as TransId',
                'Trans.CodeItemPrice as TransCode',
                'Trans.PriceGroupItemPrice'
            )
            ->get();

        $rejectionData = ErosDB::getRejectionData();

        return view('cms.receiving.specimenReceivingEdit', [ 'queue' => $queue, 'items' => $items, 'rejectiondata' => $rejectionData]);
        
    }

    public function editImaging(Request $request, $id)
    {
        $subgroup = explode(',', $request->query('subgroup'));      
        $queue = DB::connection('CMS')->table('Queue')->where('Id', $id)->first();

        // Maybe slightly different query or additional conditions
        $items = DB::connection('CMS')
            ->table('AccessionNo as Acc')
            ->leftJoin('QueueStatus as QueueStat', 'Acc.Status', '=', 'QueueStat.Id')
            ->leftJoin('Transactions as Trans', 'Acc.IdTransaction', '=', 'Trans.Id')
            ->leftJoin('Eros.ItemMaster as Item', 'Acc.ItemCode', '=', 'Item.Code')
            ->where('Acc.IdQueue', $id)
            ->where(function($query) {
    		$query->whereBetween('Acc.Status', [300, 380])
            ->orWhereIn('Acc.Status', [500, 600]);
	    })
            ->whereIn('Item.SubGroup', $subgroup)
            ->select(
                'Acc.IdTransaction', 'Acc.IdQueue', 'Acc.ItemCode', 'Acc.ItemDescription',
                'QueueStat.Name as StatusName',
                'Trans.Id as TransId',
                'Trans.CodeItemPrice as TransCode',
                'Trans.PriceGroupItemPrice'
            )
            ->get();

        $rejectionData = ErosDB::getRejectionData();

        return view('cms.receiving.specimenReceivingEdit', ['queue' => $queue, 'items' => $items, 'rejectiondata' => $rejectionData]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function receiveSpecimen(Request $request, $id)
    {
        $queueCode = $request->input('queueCode');
        $selectedItems = $request->input('rows');
        $subgroup = explode(',', $request->query('subgroup'));
        $type = $request->query('type', 'specimen');

        // Check if this is blood-related (HEMATOLOGY subgroup)
        $isBloodRelated = in_array('HEMATOLOGY', $subgroup);

        // Generate ReceiveBatchCode only if blood-related
        $batchCode = null;
        if ($isBloodRelated) {
            // Fetch all existing batch codes for this QueueCode
            $existingBatches = Receiving::where('QueueCode', $queueCode)
                ->whereNotNull('ReceivingBatchCode')
                ->pluck('ReceivingBatchCode')
                ->toArray();

            // Find the highest letter suffix used so far
            $lastLetter = null;
            foreach ($existingBatches as $batch) {
                $suffix = substr($batch, strlen($queueCode));
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
            $batchCode = $queueCode . $nextLetter;
        }

        foreach ($selectedItems as $item) {
            $receiving = new Receiving();
            $receiving->IdQueue       = $id;
            $receiving->QueueCode     = $queueCode;
            $receiving->IdBUFrom      = session('userClinicCode');
            $receiving->IdTransaction = $item['IdTransaction'];
            $receiving->ItemCode      = $item['ItemCode'];

            if ($item['PriceGroupItemPrice'] === 'Package') {
                $receiving->PackageCode = $item['TransCode'];
            }
            $receiving->DateReceived = now();
            $receiving->ReceivedBy   = Auth::user()->username;

            if (
                isset($item['Tubes']) && is_array($item['Tubes']) && in_array('HEMATOLOGY', $subgroup)
            ) {
                $receiving->Tubes = json_encode($item['Tubes']);
                $receiving->TubesSent = json_encode(['purple' => "0", 'yellow' => "0", 'blue'=> "0", 'red'=> "0", 'gray'=> "0"]);
                $receiving->TubesReject = json_encode(['purple' => "0", 'yellow' => "0", 'blue'=> "0", 'red'=> "0", 'gray'=> "0"]);
            } else {
                $receiving->Tubes = null;
                $receiving->TubesSent = null;
                $receiving->TubesReject = null;
            }

            if ($isBloodRelated) {
                $receiving->ReceivingBatchCode = $batchCode;
            }

            $receiving->Notes       = $item['Notes'];
            // $receiving->SendOutDate = now();
            // $receiving->SendOutBy   = Auth::user()->username;
            if (!empty($item['Reject']) && $item['Reject'] == true) {
                $receiving->Status = 'Rejected';
                $receiving->Reason = $item['RejectReason'] ?? null;
                $receiving->RejectBy = Auth::user()->username;
                $receiving->RejectDateTime = now();
                // $receiving->RejectedBy = Auth::user()->username;
            } elseif (!empty($item['Refused']) && $item['Refused'] == true) { 
                $receiving->Status = 'Refused';
                $receiving->RejectBy = Auth::user()->username;
                $receiving->RejectDateTime = now();
            } elseif (!empty($item['Waived']) && $item['Waived'] == true) {
                $receiving->Status = 'Waived';
                $receiving->RejectBy = Auth::user()->username;
                $receiving->RejectDateTime = now();
            } elseif (!empty($item['DoneOutside']) && $item['DoneOutside'] == true) {
                $receiving->Status = 'Done Outside';
                $receiving->RejectBy = Auth::user()->username;
                $receiving->RejectDateTime = now();
            } else {
                $receiving->Status = 'Specimen Received';
            }

            $receiving->save();

            if (!empty($item['Reject']) && $item['Reject'] == true) {
                $accessionStatus = 877; // Rejected
            } elseif (!empty($item['Refused']) && $item['Refused'] == true) {
                $accessionStatus = 899; // Refused
            } elseif (!empty($item['Waived']) && $item['Waived'] == true) {
                $accessionStatus = 888; // Waived
            } elseif (!empty($item['DoneOutside']) && $item['DoneOutside'] == true) {
                $accessionStatus = 866; // Waived
            } else {
                $accessionStatus = ($type === 'imaging') ? 311 : 311;
            }

            $updateData = ['Status' => $accessionStatus];

            if ($accessionStatus == 311) {
                $updateData['ReceivedBU'] = session('userClinicCode');
                $updateData['ExamDate'] = now();
            }

            DB::connection('CMS')->table('AccessionNo')
                ->where('IdTransaction', $item['IdTransaction'])
                ->where('ItemCode', $item['ItemCode'])
                ->update($updateData);

            $all311 = DB::connection('CMS')->table('AccessionNo')
                ->where('IdTransaction', $item['IdTransaction'])
                ->whereNotIn('ItemGroup', ['CLINIC'])
                ->where('Status', 311)
                ->count();

            $all = DB::connection('CMS')->table('AccessionNo')
                ->where('IdTransaction', $item['IdTransaction'])
                ->whereNotIn('ItemGroup', ['CLINIC'])
                ->count();

            $has304 = DB::connection('CMS')->table('AccessionNo')
                ->where('IdTransaction', $item['IdTransaction'])
                ->whereNotIn('ItemGroup', ['CLINIC'])
                ->where('Status', 304)
                ->exists();

            $has888 = DB::connection('CMS')->table('AccessionNo')
                ->where('IdTransaction', $item['IdTransaction'])
                ->whereNotIn('ItemGroup', ['CLINIC'])
                ->where('Status', 888)
                ->exists();

            $has877 = DB::connection('CMS')->table('AccessionNo')
                ->where('IdTransaction', $item['IdTransaction'])
                ->whereNotIn('ItemGroup', ['CLINIC'])
                ->where('Status', 877)
                ->exists();

            $has899 = DB::connection('CMS')->table('AccessionNo')
                ->where('IdTransaction', $item['IdTransaction'])
                ->whereNotIn('ItemGroup', ['CLINIC'])
                ->where('Status', 899)
                ->exists();

            // Default transaction status
            $transactionStatus = ($type === 'imaging') ? 311 : 311; // Specimen Received (fully)

            // If any accession is not 311, mark partial or exception
            if ($has304 || $has888 || $has877 || $has899 || $all311 < $all) {
                if (!empty($item['Waived']) && $item['Waived'] == true) {
                    if ($item['PriceGroupItemPrice'] === 'Package') {
                        $transactionStatus = 313; // Partial received for package
                    } else {
                        $transactionStatus = 888; // Fully waived (non-package)
                    }
                } elseif (!empty($item['Reject']) && $item['Reject'] == true) {
                    if ($item['PriceGroupItemPrice'] === 'Package') {
                        $transactionStatus = 313; // Partial received for package
                    } else {
                        $transactionStatus = 877; // Fully rejected (non-package)
                    }
                } elseif (!empty($item['Refused']) && $item['Refused'] == true) {
                    if ($item['PriceGroupItemPrice'] === 'Package') {
                        $transactionStatus = 313; // Partial received for package
                    } else {
                        $transactionStatus = 899; // Fully refused (non-package)
                    }
                } else {
                    $transactionStatus = 313; // Partial received (mixed)
                }
            }

            // Update the transaction status
            DB::connection('CMS')->table('Transactions')
                ->where('Id', $item['IdTransaction'])
                ->update(['Status' => $transactionStatus]);


            DB::connection('CMS')->table('Transactions')
                ->where('Id', $item['IdTransaction'])
                ->update(['Status' => $transactionStatus]);
        }

        if ($type === 'imaging') {
            $statusToCheck = [370, 380, 312];
        } else {
            $statusToCheck = [300];
        }

        // Check for remaining items BEFORE modifying kiosk station
        $remainingItems = DB::connection('CMS')
            ->table('AccessionNo as Acc')
            ->leftJoin('QueueStatus as QueueStat', 'Acc.Status', '=', 'QueueStat.Id')
            ->leftJoin('Transactions as Trans', 'Acc.IdTransaction', '=', 'Trans.Id')
            ->leftJoin('Eros.ItemMaster as Item', 'Acc.ItemCode', '=', 'Item.Code')
            ->where('Acc.IdQueue', $id)
            ->when($statusToCheck, function ($query) use ($statusToCheck) {
                if (count($statusToCheck) > 1) {
                    return $query->whereIn('Acc.Status', $statusToCheck);
                } else {
                    return $query->where('Acc.Status', $statusToCheck[0]);
                }
            })
            ->when($subgroup, function ($query) use ($subgroup) {
                return $query->whereIn('Item.SubGroup', $subgroup);
            })
            ->select(
                'Acc.IdTransaction', 'Acc.IdQueue', 'Acc.ItemCode', 'Acc.ItemDescription',
                'QueueStat.Name as StatusName',
                'Trans.Id as TransId',
                'Trans.CodeItemPrice as TransCode',
                'Trans.PriceGroupItemPrice',
                'Item.SubGroup'
            )
            ->get();

        // Only remove MICROSCOPY if there are NO remaining items
        $kiosk = Kiosk::where('IdQueueCMS', $id)->first();
        
        if ($kiosk) {
            $stations = explode(', ', $kiosk->Station);

            foreach ($subgroup as $group) {
                $hasRemainingInGroup = $remainingItems->contains(function ($item) use ($group) {
                    return $item->SubGroup === $group;
                });

                if (!$hasRemainingInGroup) {
                    $stations = array_filter($stations, fn($station) => $station !== $group);
                }
            }

            if (count($stations) === 0) {
                $kiosk->update([
                    'Station' => 'exit',
                    'Status' => 'complete',
                    'CurrentRoom' => 'Lobby'
                ]);
            } else {
                $kiosk->update([
                    'Station' => implode(', ', $stations),
                    'Status' => 'next_room',
                    'CurrentRoom' => 'Lobby',
                    'numOfCall' => '0'
                ]);
            }
        }

        // If no more remaining items, redirect
        if ($remainingItems->isEmpty()) {
            if ($type === 'bloodextraction') {
                $redirectRoute = route('bloodextraction.index');
            } elseif ($type === 'imaging') {
                $redirectRoute = route('imaging.index');
            } else {
                $redirectRoute = route('specimen.index');
            }

            return response()->json([
                'remainingItems' => [],
                'redirect' => $redirectRoute
            ]);
        }

        // Else, return remaining items as JSON
        return response()->json([
            'remainingItems' => $remainingItems
        ]);
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
