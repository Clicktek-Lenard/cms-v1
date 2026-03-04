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

class OutsidePhysicianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $physicianData = ErosDB::getPhysicianData();

        return view('cms/pages.outsidePhysician', ['physicianData' => $physicianData]);
    }
    
    public function store(Request $request, $id)
    {
	//
    }


    public function edit(Request $request)
    {

        // $datas = ErosDB::getPhysicianData($id);
        
	    // return view('eros.physicianListEdit', ['datas' => $datas, 'postLink' => url(session('userBUCode').'/erosui/physician/'.$id)]); 
    }
   
    public function update(Request $request, $id)
    {
	//
    }
    
    

    public function show()
    {

    }

    public function searchPhysician(Request $request)
    {
        $physicianDatas = ErosDB::getPhysicianDatas()
        ->where('Status', 'NOT LIKE', 'Inactive')
        ->where(function ($query) use ($request) {
            $query->orWhere('PRCNo', 'LIKE', "{$request->prcNo}%")
                ->orWhere('LastName', 'LIKE', "%{$request->lname}%")
                ->orWhere('FirstName', 'LIKE', "%{$request->fname}%")
                ->orWhere('MiddleName', 'LIKE', "%{$request->mname}%");
        })
        ->select('Id', 'LastName', 'FirstName', 'MiddleName', 'Description', 'PRCNo', 'Status', DB::raw("
            CASE
                WHEN PRCNo LIKE '{$request->prcNo}%' THEN 1 ELSE 0 END +
            CASE
                WHEN LastName LIKE '%{$request->lname}%' THEN 1 ELSE 0 END + 
            CASE
                WHEN FirstName LIKE '%{$request->fname}%' THEN 1 ELSE 0 END +
            CASE
                WHEN MiddleName LIKE '%{$request->mname}%' THEN 1 ELSE 0 END
            AS relevance"))
        ->orderBy('relevance', 'desc')
        ->get(); 

    return response()->json(['physicianDatas' => $physicianDatas]);

    }
    
    
    // public function searchPhysician(Request $request)
    // {
    //     $physicianDatas = ErosDB::getPhysicianDatas()
    //     // ->where('Status', 'Active')
    //         ->where(function ($query) use ($request) {
    //             if ($request->filled('prcNo')) {
    //                 $query->orWhere('PRCNo', 'LIKE', "{$request->prcNo}%");
    //             }
    //             if ($request->filled('lname')) {
    //                 $query->orWhere('LastName', 'LIKE', "%{$request->lname}%");
    //             }
    //             if ($request->filled('fname')) {
    //                 $query->orWhere('FirstName', 'LIKE', "%{$request->fname}%");
    //             }
    //             if ($request->filled('mname')) {
    //                 $query->orWhere('MiddleName', 'LIKE', "%{$request->mname}%");
    //             }
    //          })
    //     ->select('Id', 'LastName', 'FirstName', 'MiddleName', 'Description', 'PRCNo', DB::raw("
    //         CASE
    //             WHEN PRCNo LIKE '{$request->prcNo}%' THEN 1 ELSE 0 END +
    //         CASE
    //             WHEN LastName LIKE '%{$request->lname}%' THEN 1 ELSE 0 END +
    //         CASE
    //             WHEN FirstName LIKE '%{$request->fname}%' THEN 1 ELSE 0 END +
    //         CASE
    //             WHEN MiddleName LIKE '%{$request->mname}%' THEN 1 ELSE 0 END
    //         AS relevance"))
    //     ->orderBy('relevance', 'desc')
    //     ->get(); 

    // return response()->json(['physicianDatas' => $physicianDatas]);

    // }
    // window.location.href = "{{ url(session('userBU').'/erosui/physician/73801/edit') }}";
    // public function searchPhysician(Request $request)
    // {
    //     $physicianDatas = ErosDB::getPhysicianData() // This returns a query builder now
    //         ->where('Status','Active')
    //         ->where('PRCNo', 'LIKE', "%{$request->prcNo}%")
    //         ->where('LastName', 'LIKE', "%{$request->lname}%")
    //         ->where('FirstName', 'LIKE', "%{$request->fname}%")
    //         ->where('MiddleName', 'LIKE', "%{$request->mname}%")
    //         ->get();  // Retrieve the results

    //     // Return the physician data as JSON
    //     return response()->json(['physicianDatas' => $physicianDatas]);
    // }
    
 
}
