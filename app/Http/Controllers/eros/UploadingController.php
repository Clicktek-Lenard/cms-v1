<?php
   
namespace App\Http\Controllers\cms\settings;
use App\Http\Requests;
use App\Http\Controllers\Controller;  
  
  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;

use App\Exports\cms\PatientExport;

use App\Models\cms\Patient;
use App\Models\cms\Company;
use App\Models\cms\Queue;



 
class UploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function imdApeFileUpload()
    {
        return view('cms.settings.uploadExcel');
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

	
	
	$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('uploads/IMD')."/".$fileName);
	//  Get worksheet dimensions
	$patientInfo = $spreadsheet->getSheetNames()[0];
	$companyInfo = $spreadsheet->getSheetNames()[1];
	
	if( $patientInfo !== "Patient Info" || $companyInfo != "Company Info"  )
	{
		return back()
			    ->withErrors(['msg' => 'Please use assigned excel format for this module or click download button...']);
		die();
	}
	
	$sheetCompany = $spreadsheet->getSheetByName('Company Info');
	$companyCode = $sheetCompany->rangeToArray('B' . 1 . ':I' . 1,NULL,TRUE,FALSE);
	$iCompany = DB::connection('mysql')->select("SELECT `Id` FROM `Companies` WHERE  `Code` LIKE  '".$companyCode[0][0]."' ");
			
	if(count($iCompany) == 0)
	{
		return back()
		    ->withErrors(['msg' => 'Company Code could not be found, please check you input on Company Info sheet...']);
		die();
	
	}
			
	
	
	/////////echo $rowData[0][0];
	/////////die();
	$sheet = $spreadsheet->getSheetByName('Patient Info');
	$highestRow = $sheet->getHighestRow(); 
	$highestColumn = $sheet->getHighestColumn();
	//  Loop through each row of the worksheet in turn
	for ($row = 3; $row <= $highestRow; $row++)
	{ 
		//  Read a row of data into an array
		$rowData = $sheet->rangeToArray('A' . $row . ':I' . $row,NULL,TRUE,FALSE);
		
		//echo date('Y-m-d', ($rowData[0][5] - 25569) * 86400);
		//die();
		
		
		if(!empty(trim($rowData[0][1])) && !empty(trim($rowData[0][2]))  )
		{
			// check dob format
			if(!empty(trim($rowData[0][4])) && !is_numeric($rowData[0][4]))
			{
				return back()
				    ->withErrors(['msg' => 'DOB format not recognize!. @ row-'.$row]);
				die();
			}
			
			if(!empty($rowData[0][9]) &&  !is_numeric($rowData[0][9]))
			{
				return back()
				    ->withErrors(['msg' => 'APE Date format not recognize!. @ row-'.$row]);
				die();
			}
			
			
			$employeeID 	= Str::of($rowData[0][0])->replaceMatches('/ {2,}/', ' '); 
			$lastName 	= Str::of($rowData[0][1])->replaceMatches('/ {2,}/', ' '); 
			$firstName 	= Str::of($rowData[0][2])->replaceMatches('/ {2,}/', ' ');
			$middleName 	= Str::of($rowData[0][3])->replaceMatches('/ {2,}/', ' ');
			$dob 		=  date('Y-m-d', ($rowData[0][4] - 25569) * 86400);
			$gender 		= Str::of($rowData[0][5])->replaceMatches('/ {2,}/', ' ');
			$address 		= Str::of($rowData[0][6])->replaceMatches('/ {2,}/', ' ');
			$contact 		= Str::of($rowData[0][7])->replaceMatches('/ {2,}/', ' ');
			$nationality 	= Str::of($rowData[0][8])->replaceMatches('/ {2,}/', ' ');
			//$dateAPE 		=  date('Y-m-d', ($rowData[0][9] - 25569) * 86400);
			$fullName		= Str::of($lastName.', '.$firstName.' '.$middleName)->replaceMatches('/ {2,}/', ' ');
			
			
				$pID = Patient::insertCheckPatient(
					array(
						'UploadID'	=> $fileName.'-'.$row
						,'Code'		=> $employeeID
						,'FullName'	=> strtoupper($fullName)
						,'LastName' 	=> strtoupper($lastName)
						,'FirstName' 	=> strtoupper($firstName)
						,'MiddleName' 	=> strtoupper($middleName)
						,'Suffix' 		=> ''
						,'Gender' 		=> strtoupper(substr($gender, 0, 1))
						,'DOB' 		=> date('Y-m-d', strtotime($dob))
						,'Address'		=> $address
						,'City'		=> ''
						,'ContactNo'	=> $contact
						,'Nationality'	=> $nationality
						//,'APEDate'		=> date('Y-m-d', strtotime($dateAPE))
						,'Status'		=> 'NEW'
						,'Remarks'		=> ''
						,'InputDate'	=> date('Y-m-d')
						,'InputBy'		=> Auth::user()->username
				));
				
				Queue::insertCheckQueue(
					array(
						'PatientId'		=> $pID
						,'CompanyId'	=> $iCompany[0]->Id
						,'UploadID'	=> $fileName.'-'.$row
						,'Code'		=> ''
						,'FullName'	=> strtoupper($fullName)
						,'LastName' 	=> strtoupper($lastName)
						,'FirstName' 	=> strtoupper($firstName)
						,'MiddleName' 	=> strtoupper($middleName)
						,'Suffix' 		=> ''
						,'Gender' 		=> strtoupper(substr($gender, 0, 1))
						,'DOB' 		=> date('Y-m-d', strtotime($dob))
						,'Address'		=> $address
						,'City'		=> ''
						,'ContactNo'	=> $contact
						,'Nationality'	=> $nationality
						//,'APEDate'		=> date('Y-m-d', strtotime($dateAPE))
						,'Status'		=> 'NEW'
						,'Remarks'		=> ''
						,'InputDate'	=> date('Y-m-d')
						,'InputBy'		=> Auth::user()->username
				));
		}
		else
		{
			return back()
			    ->withErrors(['msg' => 'Required field should not be empty!. @ row-'.$row]);
			die();	
		}
	}
	

	//return Excel::download(new PatientExport($fileName), 'CMS Patient Onsite.xlsx');
	
	return back()
            ->with('success','You have successfully upload file.')
            ->with('file',$fileName);
	//Excel::download(new PatientExport($fileName), 'IMD Patient - Eros.xlsx');
   
    }
}