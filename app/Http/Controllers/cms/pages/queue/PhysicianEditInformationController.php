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

class PhysicianEditInformationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    
    public function create()
    {

	  //
    }

    
    public function edit(Request $request, $id)
    {
        $physicianEditDatas = ErosDB::getPhysicianData($id); //data from physicianInfoEdit.blade.php
        //  dd($physicianEditDatas);


        return view('cms.pages.physicianInfoEdit', ['physicianEditDatas' => $physicianEditDatas]);
    }

   
    public function editphysician(Request $request)
    {
        $physicianId = $request->input('selectedId');
        $fullnameSpace = $request->input('lastname') . ", " . $request->input('firstname') . " " . $request->input('middlename');
        $fullname = strtoupper(Str::of($fullnameSpace)->replaceMatches('/ {2,}/', ' '));
        // $idqueue = $request->input('idqueue');
        DB::connection('CMS')->beginTransaction();
        DB::connection('Eros')->table('Physician')
            ->where('Id', $physicianId)
            ->update([
                'LastName' => strtoupper(Str::of($request->input('lastname'))->replaceMatches('/ {2,}/', ' ')),
                'FirstName' => strtoupper(Str::of($request->input('firstname'))->replaceMatches('/ {2,}/', ' ')),
                'MiddleName' => strtoupper(Str::of($request->input('middlename'))->replaceMatches('/ {2,}/', ' ')),
                'FullName' => $fullname,
                'DisplayName' => $fullname,
                'PRCNo' => $request->input('prcno'),
                'Description' => strtoupper(Str::of($request->input('description'))->replaceMatches('/ {2,}/', ' ')),
                'Status' => 'RP - For Approval',
            ]);

            // dd($_GET['idQueue']);
            DB::connection('CMS')->table('Transactions')
            ->where('IdDoctor',  $physicianId)                                      //added 12-11-24 for updating physician name in table trx
            ->where('IdQueue', $_GET['idQueue'])
            ->update([
                'NameDoctor'        => $fullname,
                // 'UpdateDateTime'	=> date('Y-m-d'),
                'UpdateBy'	    => Auth::user()->username,
                'Status' => 212
            ]);
            DB::connection('CMS')->table('Queue')
            ->where('Id', $_GET['idQueue'])
            ->update([
                'Status' => 212
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
            
                DB::connection('Eros')->table('Physician')->where('Id', $physicianId)->update(['Prescription_Link' => $fileName]);
            }
            
            DB::connection('CMS')->commit();
        return response()->json(['success' => true]);
    }
    
    public function show(Request $request,$id)
    {
       
    } 
 
}
