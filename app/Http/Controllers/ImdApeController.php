<?php
   
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;


use App\Exports\eros\PatientExport;

use App\Models\eros\ErosDB;

  
class ImdApeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function imdApeFileUpload()
    {
        return view('imdApeFileUpload');
    }
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function imdApeFileUploadPost(Request $request)
    {
	
	
        $request->validate([
            'file' => 'required|mimes:xlsx|max:2048',
        ]);
  
        $fileName = time().'.'.$request->file->extension();  
        $request->file->move(public_path('uploads/IMD'), $fileName);

	
	DB::connection('Eros')->beginTransaction();
	$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('uploads/IMD')."/".$fileName);
	//  Get worksheet dimensions
	$sheet = $spreadsheet->getActiveSheet(); 
	$highestRow = $sheet->getHighestRow(); 
	$highestColumn = $sheet->getHighestColumn();
	//  Loop through each row of the worksheet in turn
	for ($row = 3; $row <= $highestRow; $row++)
	{ 
		//  Read a row of data into an array
		$rowData = $sheet->rangeToArray('A' . $row . ':J' . $row,NULL,TRUE,FALSE);
		
		//echo date('Y-m-d', ($rowData[0][5] - 25569) * 86400);
		//die(2022-10-17 23:57:19);
		
		
		if(!empty(trim($rowData[0][0])) && !empty(trim($rowData[0][1])) && !empty(trim($rowData[0][3])) && !empty(trim($rowData[0][4])) && !empty(trim($rowData[0][8])) && !empty(trim($rowData[0][9])) )
		{
			// check dob format
			if(!is_numeric($rowData[0][3]))
			{
				return back()
				    ->withErrors(['msg' => 'DOB format not recognize!. @ row-'.$row]);
				die();
			}
			
			if(!is_numeric($rowData[0][8]))
			{
				return back()
				    ->withErrors(['msg' => 'APE Date format not recognize!. @ row-'.$row]);
				die();
			}
			
			if(!empty($rowData[0][9]) && strtoupper(trim($rowData[0][9])) == 'YES'  )
			{
				$wTest = "ECG";
			}else{
				$wTest = "";
			}
			
		
			$dob =  date('Y-m-d', ($rowData[0][3] - 25569) * 86400);
			$dateAPE =  date('Y-m-d', ($rowData[0][8] - 25569) * 86400);
			
			if($dateAPE == '1899-12-30')
			{
				return back()
				    ->withErrors(['msg' => 'APE Date not found!. @ Column I : row-'.$row]);
				die();
			}
			
			$lastName 	= Str::of($rowData[0][0])->replaceMatches('/ {2,}/', ' '); 
			$firstName 	= Str::of($rowData[0][1])->replaceMatches('/ {2,}/', ' ');
			$middleName 	= Str::of($rowData[0][2])->replaceMatches('/ {2,}/', ' ');
			$gender 		= Str::of($rowData[0][4])->replaceMatches('/ {2,}/', ' ');
			$address 		= Str::of($rowData[0][5])->replaceMatches('/ {2,}/', ' ');
			$contact 		= Str::of($rowData[0][6])->replaceMatches('/ {2,}/', ' ');
			$nationality 	= Str::of($rowData[0][7])->replaceMatches('/ {2,}/', ' ');
			$fullName		= Str::of($lastName.', '.$firstName.' '.$middleName)->replaceMatches('/ {2,}/', ' ');
			// check Eros DB if exist 
			$checkIfExistEros = ErosDB::checkErosNameInsertTemp(
				array(
					'UploadID'		=> $fileName.'-'.$row
					,'LastName' 	=> strtoupper($lastName)
					,'FirstName' 	=> strtoupper($firstName)
					,'Gender' 		=> strtoupper(substr($gender, 0, 1))
					,'DOB' 		=> \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d', strtotime($dob)))->format('d-M-Y')
					,'DOBORG'		=> \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d', strtotime($dob)))->format('Y-m-d')
					,'Address'		=> $address
					,'APEDate'		=>date('Y-m-d', strtotime($dateAPE))
					,'wTest'		=> $wTest
				));
			// check local DB if exist	
			$checkIfExistLocal = ErosDB::checkLocalName(
				array(
					'UploadID'		=> $fileName.'-'.$row
					,'LastName' 	=> strtoupper($lastName)
					,'FirstName' 	=> strtoupper($firstName)
					,'Gender' 		=> strtoupper(substr($gender, 0, 1))
					,'DOB' 		=> date('Y-m-d', strtotime($dob))
					,'Address'		=> $address
					,'APEDate'		=> date('Y-m-d', strtotime($dateAPE))
					,'wTest'		=> $wTest
				));
				
				
			// both empty insert local db	
			if($checkIfExistEros == 0 && $checkIfExistLocal == 0)
			{	// insert both temp and patient
				ErosDB::insertNewPatient(
					array(
						'UploadID'		=> $fileName.'-'.$row
						,'Code'		=> ErosDB::getPatientMAX('I')
						,'FullName'	=> strtoupper($fullName)
						,'LastName' 	=> strtoupper($lastName)
						,'FirstName' 	=> strtoupper($firstName)
						,'MiddleName' 	=> strtoupper($middleName)
						,'Suffix' 		=> ''
						,'Gender' 		=> strtoupper(substr($gender, 0, 1))
						,'DOB' 		=> date('Y-m-d', strtotime($dob))
						,'Address'		=>  $address
						,'City'		=> ''
						,'ContactNo'	=> $contact
						,'Nationality'	=> $nationality
						,'APEDate'		=> date('Y-m-d', strtotime($dateAPE))
						,'Status'		=> 'NEW'
						,'Remarks'		=> ''
						,'InputDate'	=> date('Y-m-d')
						,'InputBy'		=> 'RAV'
						,'wTest'		=> $wTest
						
				));
			
			}
		}
		else
		{
			return back()
			    ->withErrors(['msg' => 'Required field should not be empty from Column A to J @ row-'.$row]);
			die();	
		}
	}
	DB::connection('Eros')->commit(); 

	return Excel::download(new PatientExport($fileName), 'IMD Patient - Eros.xlsx');
	
	return back()
            ->with('success','You have successfully upload file.')
            ->with('file',$fileName);
	//Excel::download(new PatientExport($fileName), 'IMD Patient - Eros.xlsx');
   
    }
}