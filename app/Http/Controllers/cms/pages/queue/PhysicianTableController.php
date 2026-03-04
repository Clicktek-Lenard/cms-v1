<?php

namespace App\Http\Controllers\cms\pages\queue;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Database\Query\Builder;
use App\Models\eros\ErosDB;

class PhysicianTableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    //   dd($_GET['id']);
    $physicianDatas = ErosDB::getPhysicianData($_GET['id']); //data from physicianInfo.blade.php

      return view('cms.pages.physicianInfo',['physicianDatas' => $physicianDatas] );
    }
    
    public function create()
    {

	    return view('cms/pages.physicianEnrollment', ['postLink' =>url('cms/queue/pages/physicianEnrollment')]);//physician Enrollment modal
    }

    
    public function edit(Request $request, $id)
    {
       //
    }

   
    public function physicianupdate(Request $request)
    {
        $physicianId = $request->input('selectedId');
        $fullnameSpace = $request->input('lastname') . ", " . $request->input('firstname') . " " . $request->input('middlename');
        $fullname = strtoupper(Str::of($fullnameSpace)->replaceMatches('/ {2,}/', ' '));
        // $idqueue = $request->input('idqueue');

        DB::connection('Eros')->table('Physician')
            ->where('Id', $physicianId)
            ->update([
                'LastName' => strtoupper(Str::of($request->input('lastname'))->replaceMatches('/ {2,}/', ' ')),
                'FirstName' => strtoupper(Str::of($request->input('firstname'))->replaceMatches('/ {2,}/', ' ')),
                'MiddleName' => strtoupper(Str::of($request->input('middlename'))->replaceMatches('/ {2,}/', ' ')),
                'FullName' => $fullname,
                'DisplayName' => $fullname,
                'PRCNo' => $request->input('prcno'),
                'BranchCode'    => session('userClinicCode'),              
                'Description' => $request->input('description'),
                'Status' => 'RP - For Approval',
                'RequestorBy'   => Auth::user()->username,
                'UpdateBy'      => Auth::user()->username,
            ]);

            // DB::connection('CMS')->table('Queue')
            // ->where('Id', $_GET['idQueue'])
            // ->update([
            //     'AnteDateReason' => "Physician For Enrollment"
            // ]);

            $img = $request->input('myimage');

            if (!empty($img) && str_contains($img, 'base64,')) {                    // save the image including the name of physician in the filename
                $folderPath = public_path('uploads/PhysicianPrescription/');
                $image_parts = explode(";base64,", $img);
                $image_base64 = base64_decode($image_parts[1]);
                $safeFullname = preg_replace('/[^A-Za-z0-9 ]/', '', $fullname);
                $safeFullname = str_replace(' ', '_', trim($safeFullname)); 
                $fileName = $safeFullname . '.png';
                $file = $folderPath . $fileName;
    
                file_put_contents($file, $image_base64);
            
                DB::connection('Eros')->table('Physician')->where('Id', $physicianId)->update(['Prescription_Link' => $fileName]);
            }
                       // dd($_GET['idQueue']);
        //     DB::connection('CMS')->table('Transactions')
        //     ->where('IdQueue', $_GET['idQueue'])
        //     ->update([
        //         'NameDoctor'        => $request->input('fullname'),
        //         // 'UpdateDateTime'	=> date('Y-m-d'),
        //         'UpdateBy'	    => Auth::user()->username,
        //         'Status' => 212
        //     ]);
             

        return response()->json(['success' => true]);
    }

    public function insertPhysician(Request $request)
    {
        $fullnameSpace = $request->input('lastname') . ", " . $request->input('firstname') . " " . $request->input('middlename');
        $fullname = strtoupper(Str::of($fullnameSpace)->replaceMatches('/ {2,}/', ' '));
        $erosCode = strtoupper(Str::of(substr($request->input('lastname'), 0, 1) . substr($request->input('firstname'), 0, 1) . substr($request->input('middlename'), 0, 1))->replaceMatches('/ {2,}/', ' '));
    
        // Normalize PRC number by removing leading zeros
        $prcNo = ltrim($request->input('prcno'), '0');  

        // Check if the PRC number already exists (ignoring leading zeros)
        $existingPhysician = DB::connection('Eros')->table('Physician')
            ->whereRaw("LPAD(PRCNo, 7, '0') = ?", [str_pad($prcNo, 7, '0', STR_PAD_LEFT)])
            ->first();

        if ($existingPhysician) {
            return response()->json(['id' => $existingPhysician->Id, 'exists' => true]);
        }
    
        $erosCodeMax = ErosDB::getPhysicianMAX($erosCode);
    
        $newPhysicianId = DB::connection('Eros')->table('Physician')
            ->insertGetId([
                'FullName' => $fullname,
                'DisplayName' => $fullname,
                'LastName' => strtoupper(Str::of($request->input('lastname'))->replaceMatches('/ {2,}/', ' ')),
                'FirstName' => strtoupper(Str::of($request->input('firstname'))->replaceMatches('/ {2,}/', ' ')),
                'MiddleName' => strtoupper(Str::of($request->input('middlename'))->replaceMatches('/ {2,}/', ' ')),
                'PRCNo' => $request->input('prcno'),
                'Description' => strtoupper(Str::of($request->input('description'))->replaceMatches('/ {2,}/', ' ')),
                'ErosCode' => $erosCodeMax,
                'Code' => $erosCodeMax,
                'BranchCode'    => session('userClinicCode'),
                'RequestorBy'   => Auth::user()->username,
                'Status' => 'RP - For Approval',
                'SubGroup'  => 'RP',
                'InputDate' => date('Y-m-d'),
                'InputBy' => Auth::user()->username,
        ]);
    
        $img = $request->input('myimage');
        
        if (!empty($img) && str_contains($img, 'base64,')) {                    // save the image including the name of physician in the filename
            $folderPath = public_path('uploads/PhysicianPrescription/');
            $image_parts = explode(";base64,", $img);
            $image_base64 = base64_decode($image_parts[1]);
            $safeFullname = preg_replace('/[^A-Za-z0-9 ]/', '', $fullname);
            $safeFullname = str_replace(' ', '_', trim($safeFullname)); 
            $fileName = $safeFullname . '.png';
            $file = $folderPath . $fileName;

            file_put_contents($file, $image_base64);
        
            DB::connection('Eros')->table('Physician')->where('Id', $newPhysicianId)->update(['Prescription_Link' => $fileName]);
        }
    // dd($newPhysicianId);
        return response()->json(['id' => $newPhysicianId, 'exists' => false, 'fullname' => $fullname]);
    }
    
    public function show(Request $request,$id)
    {
       
    } 
 
}
