<?php
   
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

  
class FileUploadController extends Controller
{
	public  function dropzoneUi()  
	{  
		return view('upload-view');  
	}  
	/** 
	* File Upload Method 
	* 
	* @return void 
	*/  
	public  function dropzoneFileUpload(Request $request)  
	{  
		$image = $request->file('file');
		$imageName = time().'.'.$image->extension(); 
		$image->move(public_path('APE'),$imageName);  
		return response()->json(['success'=>$imageName]);
	}

public function fileUpload(Request $request){

        $path = 'uploads/';
        $file = $request->file('file');

        $filename      = $file->getClientOriginalName();
        $file->move(public_path($path), $filename);

        $fileupload = new FileUpload();
        $fileupload->file_name = $filename;
        $fileupload->save();

        return response()->json(['success'=>$filename]);

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function airChinaFileUpload()
    {
        return view('airChinaFileUpload');
    }
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function airChinaFileUploadPost(Request $request)
    {
    
        $request->validate([
            'file' => 'required|mimes:xlsx|max:2048',
        ]);
  
        $fileName = time().'.'.$request->file->extension();  
        $request->file->move(public_path('uploads'), $fileName);

	
	DB::connection('DnCMS')->beginTransaction();
	$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('uploads')."/".$fileName);
	//  Get worksheet dimensions
	$sheet = $spreadsheet->getActiveSheet(); 
	$highestRow = $sheet->getHighestRow(); 
	$highestColumn = $sheet->getHighestColumn();
	//  Loop through each row of the worksheet in turn
	for ($row = 2; $row <= $highestRow; $row++)
	{ 
		//  Read a row of data into an array
		$rowData = $sheet->rangeToArray('A' . $row . ':P' . $row,NULL,TRUE,FALSE);
		
		if(!empty($rowData[0][9]))
		{
			//  Use foreach loop and insert data into Query
			//echo $rowData[0][4] ." = ".date('Y-m-d', strtotime($rowData[0][4])) ." =>".$rowData[0][2]. ", ".$rowData[0][3] ;   
			if( date('Y-m-d', strtotime($rowData[0][1])) == "1970-01-01")
			{
				return back()
				->withErrors(['msg' => 'Check your departure date format!. row-'.$row]);
				die();
			}else if( date('Y-m-d', strtotime($rowData[0][4])) == "1970-01-01")
			{
				return back()
				->withErrors(['msg' => 'Check your date of birth format!. row-'.$row]);
				die();
			}else if( empty(trim($rowData[0][2])) )
			{
				return back()
				->withErrors(['msg' => 'Lastname should not be empty!. row-'.$row]);
				die();
			}else if( empty(trim($rowData[0][3])) )
			{
				return back()
				->withErrors(['msg' => 'Firstname should not be empty!. row-'.$row]);
				die();
			}else if( empty(trim($rowData[0][5])) )
			{
				return back()
				->withErrors(['msg' => 'Gender should not be empty!. row-'.$row]);
				die();
			}else if( empty(trim($rowData[0][12])) )
			{
				return back()
				->withErrors(['msg' => 'Photo should not be empty!. row-'.$row]);
				die();
			}
			
		
		
		
			$patients = DB::connection('DnCMS')->insert("
				INSERT INTO  patient_airchina (`departureDate`, `lastname`, `firstname`, `middlename`, `dob`, `gender`, `address`, `mobile`, `nationality`, `passportNo`, `airline`,  `email`, `photoLink`,`flightNo`) VALUES
				(
				'".date('Y-m-d', strtotime($rowData[0][1]))."'
				,'".trim($rowData[0][2])."'
				,'".trim($rowData[0][3])."'
				,'".trim($rowData[0][15])."'
				,'".date('Y-m-d', strtotime($rowData[0][4]))."'
				,'".trim($rowData[0][5])."'
				,'".trim($rowData[0][6])."'
				,'".trim($rowData[0][7])."'
				,'".trim($rowData[0][8])."'
				,'".trim($rowData[0][9])."'
				,'".trim($rowData[0][10])."'
				,'".trim($rowData[0][11])."'
				,'".trim($rowData[0][12])."'
				,'".trim($rowData[0][13])."'
				)
				");
			//print_r($rowData[0][2]);
			//echo "<br>";
		}
		else
		{
			return back()
			    ->withErrors(['msg' => 'Passport Number should not be empty!. row-'.$row]);
			die();	
		}
	}
	DB::connection('DnCMS')->commit();  
		 
	return back()
            ->with('success','You have successfully upload file.')
            ->with('file',$fileName);
   
    }
}