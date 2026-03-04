<?php
   
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

  
class QrcodeController extends Controller
{

	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fileUpload()
    {
        return view('qrcodeFileUpload');
    }
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function fileUploadPost(Request $request)
    {
    
        $request->validate([
            'file' => 'required|mimes:xlsx|max:2048',
        ]);
  
        $fileName = time().'.'.$request->file->extension();  
        $request->file->move(public_path('uploads/IMD'), $fileName);

	
	DB::connection('mysql')->beginTransaction();
	$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('uploads/IMD')."/".$fileName);
	//  Get worksheet dimensions
	$sheet = $spreadsheet->getActiveSheet(); 
	$highestRow = $sheet->getHighestRow(); 
	$highestColumn = $sheet->getHighestColumn();
	//  Loop through each row of the worksheet in turn
	for ($row = 2; $row <= $highestRow; $row++)
	{ 
		//  Read a row of data into an array
		$rowData = $sheet->rangeToArray('A' . $row . ':I' . $row,NULL,TRUE,FALSE);
		
		if(!empty(trim($rowData[0][0])) && !empty(trim($rowData[0][1])) && !empty(trim($rowData[0][4])) && !empty(trim($rowData[0][5])) 
		    && !empty(trim($rowData[0][6])) && !empty(trim($rowData[0][7])) && !empty(trim($rowData[0][8])) )
		{
			
		}
		else
		{
			return back()
			    ->withErrors(['msg' => 'Required field should not be empty!. row-'.$row]);
			die();	
		}
	}
	DB::connection('mysql')->commit();  
		 
	return back()
            ->with('success','You have successfully upload file.')
            ->with('file',$fileName);
   
    }
}