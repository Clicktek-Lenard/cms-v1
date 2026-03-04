<?php

namespace App\Http\Controllers\cms;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use App\Models\cms\Queue;
use App\Models\cms\Transactions;
use App\Models\eros\ErosDB;

class PhysicianWebcamController extends Controller
{
  
    public function index()
    {
        return view('webcam');
    }
  
    public function store(Request $request)
    {
        $img = $request->image;
        echo $folderPath = public_path('uploads/PhysicianPrescription/');
        
        $image_parts = explode(";base64,", $img);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = uniqid() . '.png';
        
        $file = $folderPath . $fileName;
	file_put_contents($file,$image_base64, FILE_USE_INCLUDE_PATH);
       // Storage::put($file, $image_base64);
        
        dd('Image uploaded successfully: '.$fileName);
    }

    
}
