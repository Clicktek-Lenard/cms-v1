<?php
//
/*
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />

https://www.linuxtechi.com/install-and-use-lsyncd-on-centos-7-rhel-7/
*/

namespace App\Http\Controllers\cms;

use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


use App\Http\Controllers\Controller;

use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use setasign\Fpdi\Tcpdf\Fpdi;

use DataTables;

class ErosPatientServerController extends Controller
{

    const DPI = 300;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }
    
   public function imageUniformToFill($pdf, string $imgPath, int $x = 0, int $y = 0, int $containerWidth = 210, int $containerHeight = 297, string $alignment = 'C')
    {
        list($width, $height) = $this->resizeToFit($imgPath, $containerWidth, $containerHeight);

        if ($alignment === 'R')
        {
            $pdf->Image($imgPath, $x+$containerWidth-$width, $y+($containerHeight-$height)/2, $width, $height);
        }
        else if ($alignment === 'B')
        {
            $pdf->Image($imgPath, $x, $y+$containerHeight-$height, $width, $height);
        }
        else if ($alignment === 'C')
        {
            $pdf->Image($imgPath, $x+($containerWidth-$width)/2, $y+($containerHeight-$height)/2, $width, $height);
        }
        else
        {
            $pdf->Image($imgPath, $x, $y, $width, $height);
        }
    }
    
    /**
     * Convertit des pixels en mm
     *
     * @param integer $val
     * @return integer
     */
    protected function pixelsToMm(int $val) : int
    {
        return (int)(round($val * $this::MM_IN_INCH / $this::DPI));
    }

    /**
     * Convertit des mm en pixels
     *
     * @param integer $val
     * @return integer
     */
    protected function mmToPixels(int $val) : int
    {
        return (int)(round($this::DPI * $val / $this::MM_IN_INCH));
    }

    /**
     * Resize une image
     *
     * @param string $imgPath
     * @param integer $maxWidth en mm
     * @param integer $maxHeight en mm
     * @return int[]
     */
    protected function resizeToFit(string $imgPath, int $maxWidth = 210, int $maxHeight = 297) : array
    {
        list($width, $height) = getimagesize($imgPath);
        $widthScale = $this->mmtopixels($maxWidth) / $width;
        $heightScale = $this->mmToPixels($maxHeight) / $height;
        $scale = min($widthScale, $heightScale);
        return array(
            $this->pixelsToMM($scale * $width),
            $this->pixelsToMM($scale * $height)
        );
    } 
    
    
    
    

   ///////////////////////
    public function index()
    { 
	// return route('erosserver.PatientList');
      return view('cms.erosPatientListServer');
	
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
	$datas = $this->getPatientServer(array('byId'=>$id));
	$submitStatus = $this->getResultsStatus($id, 'withname');
	return view('cms.erosPatientListServerEdit', ['submitStatus' => $submitStatus, 'datas' => $datas, 'postLink' => url(session('userBUCode').'/erosPatient/'.$id)]);    
    }
    
    
    public function getPatientList(Request $request)
    {
    
	$search_arr = $request->get('search');
	$searchValue = $search_arr['value'];

	if ($request->ajax()) {
		$model = $this->getPatientServer(array('fullname'=>$searchValue));
		return DataTables::of($model)->toJson();
	}
    
    }
    
    public function getResultsStatus($id, $name = NULL)
    {
	 $status = DB::connection('CMS')->table('ResultsStatus')->where('TransactionNo', $id)->get(array('StatusId', 'Description'));
	if( is_null($name))
	{
		return (isset($status[0]->StatusId) && $status[0]->StatusId == '1')?'Error':'Accepted';
	}
	else
	{
		return $status;
	}
    }
/**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
	//$datas = $this->getPatientServer(array('AddPatientTemp'=>true,'byId'=>$id, 'transaction' => $request->input('transaction') ));
	//return back()
         //   ->with('success','You have successfully create Bizbox record.');
           // ->with('file',$fileName);
    }
    
    public  function getPatientServer($params = array())
   {
		$lCompa = DB::connection('Eros')->table('Company')->where('ResultUploading', 'LIKE', 'Yes')->get(array('ErosCode'));
		
		$sCompa = "and cd_code IN (";
		$x= 0;
		foreach($lCompa as $compa)
		{
			if($x == 0)
			{
				$sCompa .= "'".$compa->ErosCode."'";
			}
			else
			{
				$sCompa .= ",'".$compa->ErosCode."'";
			}
			$x++;
			
		}
		
		$cstr = '(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP) (HOST =10.30.10.187)(PORT = 1521))  (CONNECT_DATA= (SID = hclab))     )';
		$conn = oci_connect("erosbs", "erosbs", $cstr, 'AL32UTF8');
		
		$sql = "SELECT PM_PID,  PM_FULLNAME , BTH_NPI ,  BTH_PID , BTH_TRXNO , BTH_TRXDT , to_char(PM_DOB, 'yyyy-mm-dd') as PM_DOB, BTH_TRXDT, CD_NAME,CD_CODE, PM_ADDRESS, PM_CITY, PM_EMAIL, PM_MOBILENO,
				PM_LASTNAME, PM_FIRSTNAME, PM_MIDNAME, PM_SUFFIX, PM_GENDER
		FROM billing_trx_hdr
		INNER JOIN patient_master on bth_pid = pm_pid
		LEFT JOIN company_details on bth_company = company_details.cd_code
		WHERE  ROWNUM <= 1000 ";
		//to_char(BTH_TRXDT, 'yyyy-mm-dd')  = '".date('Y-m-d')."' and 
		if(!empty($params['fullname']))
		{
			$sql = $sql . "and PM_FULLNAME like  '%".strtoupper($params['fullname'])."%' ";
		}
		if(!empty($params['byId']))
		{
			$sql = $sql . "and BTH_TRXNO =  '".$params['byId']."' ";
		}
		
		if( count($lCompa) != 0)
		{
			$sql .=  $sCompa.")";
		}
		
		$stid = oci_parse($conn, $sql);
		oci_execute($stid);
		$return =  array();
		while (oci_fetch($stid)) {
		//PM_FULLNAME like '%".$param."%'  AND 
		
			array_push($return, array(
				'id'			=> oci_result($stid, 'BTH_TRXNO')
				,'FullName'	=> oci_result($stid, 'PM_FULLNAME')
				,'or_no'		=> oci_result($stid, 'BTH_NPI')
				,'patient_id'	=> oci_result($stid, 'BTH_PID')
				,'trans_no'		=> oci_result($stid, 'BTH_TRXNO')
				,'order_date'	=> oci_result($stid, 'BTH_TRXDT')
				,'birthdate' 	=> date('M-d-Y', strtotime(oci_result($stid, 'PM_DOB')))
				,'created_at'	=> date('M-d-Y', strtotime(oci_result($stid, 'BTH_TRXDT')))
				,'Company'	=> oci_result($stid, 'CD_NAME')
				,'Address'		=> oci_result($stid, 'PM_ADDRESS')
				,'City'		=> oci_result($stid, 'PM_CITY')
				,'Email'		=> oci_result($stid, 'PM_EMAIL')
				,'Phone'		=> oci_result($stid, 'PM_MOBILENO')
				));
				
		}
		oci_close($conn);
		
		//DB::connection('oraTARe')->commit();
		return $return;	
	
	}
	
	
     public function dropzoneStore(Request $request)
    {
	$myDepartment = session('userDepartmentCode'); //$this->getDepartment();
	
	$image = $request->file('file');

	$path = public_path() . '/APE/'. $request->code;

	File::makeDirectory($path, $mode = 0750, true, true);
	
	if( $image->extension() == 'pdf' )
	{
		$imageName = $myDepartment.'.'.$image->extension();
		$checkFile = public_path('APE/'.$request->code.'/'.$imageName);
		 if(file_exists($checkFile)) 
		 {
			$pdf = new Fpdi();
			$addFile = time().'.'.$image->extension();
			$image->move(public_path('APE/'.$request->code),$addFile);
			
			$oldFile = $pdf->setSourceFile($checkFile);

			for ($i = 1; $i <= $oldFile; $i++) {
				$templateId = $pdf->importPage($i);
				//added to correct orientation
				$specs = $pdf->getTemplateSize($templateId);
				$pdf->AddPage($specs['height'] > $specs['width'] ? 'P' : 'L', [$specs['width'], $specs['height']] ); // org $pdf->AddPage();
				$pdf->useTemplate($templateId);
			}
			
			$newFilePath = public_path('APE/'.$request->code.'/'.$addFile);
			$newFile = $pdf->setSourceFile($newFilePath);
			for ($i = 1; $i <= $newFile; $i++) {
				$templateId = $pdf->importPage($i);
				//added to correct orientation
				$specs = $pdf->getTemplateSize($templateId);
				$pdf->AddPage($specs['height'] > $specs['width'] ? 'P' : 'L', [$specs['width'], $specs['height']] ); // org $pdf->AddPage();
				$pdf->useTemplate($templateId);
			}
			$fileName = time() . '.pdf';
			
			$pdf->Output(public_path('APE/'.$request->code.'/'.$imageName), 'F');
			
			$deleteFile = public_path('APE/'.$request->code.'/'.$addFile);
			if (file_exists($deleteFile)) {
			    unlink($deleteFile);
			}
		 }
		 else
		 {
			$image->move(public_path('APE/'.$request->code),$imageName);
		}
	}
	else if( $image->extension() == 'png' || $image->extension() == 'jpg' || $image->extension() == 'jpeg' )
	{
		$imageName = $myDepartment.'-image.'.$image->extension();
		$image->move(public_path('APE/'.$request->code),$imageName);
		
		$pdf = new Fpdi();
		$pdf->AddPage();
		$this->imageUniformToFill($pdf,public_path('APE/'.$request->code.'/'.$imageName), 10, 10);
		
		
	
		$pdf->Output(public_path('APE/'.$request->code.'/'.$myDepartment.'-image.pdf'), 'F');
	}
	
	DB::connection('CMS')->table('Results')->insertGetId([
		'SourceId'		=> $request->code,
		'SourceCode'	=> $myDepartment,
		//'DateTime'		=> date('Y-m-d H:i:s'),
		'UploadedBy'	=> Auth::user()->username,
		'OrgName'		=> $image->getClientOriginalName(),
		'Action'		=> 'Upload-'.$imageName
	]);
   
	
	return response()->json(['success'=>$imageName]);
	
    }
    
    public function getFiles(Request $request)
    {
    
	$targetDir = public_path('APE/'.$request->code.'/');
	$fileList = [];
  
	$dir = $targetDir;
	if (is_dir($dir)){
		if ($dh = opendir($dir)){
			while (($file = readdir($dh)) !== false){
				if($file != '' && $file != '.' && $file != '..'){
					$file_path = $targetDir.$file;
					if(!is_dir($file_path)){
						$size = filesize($file_path);
						$fileList[] = ['name'=>$file, 'size'=>$size, 'path'=>$file_path];
					}
				}
			}
		      closedir($dh);
		}
	}
	return json_encode($fileList);
    }
    
    public function deleteFile(Request $request)
    {
	$path = public_path('APE/'.$request->code.'/'.$request->get('filename'));
	
        if (file_exists($path)) {
            unlink($path);
        }
	$myDepartment = session('userDepartmentCode');
	
	DB::connection('CMS')->table('Results')->insertGetId([
		'SourceId'		=> $request->code,
		'SourceCode'	=> $myDepartment,
		//'DateTime'		=> date('Y-m-d H:i:s'),
		'UploadedBy'	=> Auth::user()->username,
		'Action'		=> 'Delete-'.$request->get('filename')
	]);
	
        return $request->get('filename')	;
	
    }
    
   public function updateFile(Request $request)
  {
  
	$submitStatus = $this->getResultsStatus($request->code);
	
	if( $submitStatus != 'Error')
	{
		// set password  
		//$pdf->SetProtection(['copy', 'print'], 'PANTIA123');
		
		$targetDir = public_path('APE/'.$request->code.'/');
		$fileList = [];
		$dir = $targetDir;
		
		
		if (is_dir($dir)){
			if ($dh = opendir($dir)){
				while (($file = readdir($dh)) !== false){
					if($file != '' && $file != '.' && $file != '..'){
						$file_path = $targetDir.$file;
						if(!is_dir($file_path)){
							$getEx = explode(".", $file);
							if( $getEx[1] == "pdf")
							{
								$pdf = new Fpdi();
								$iFile = $pdf->setSourceFile($file_path);
								for ($i = 1; $i <= $iFile; $i++) {
									$templateId = $pdf->importPage($i);
									//added to correct orientation
									$specs = $pdf->getTemplateSize($templateId);
									$pdf->AddPage($specs['height'] > $specs['width'] ? 'P' : 'L', [$specs['width'], $specs['height']] ); // org $pdf->AddPage();
									$pdf->useTemplate($templateId);
								}
								//$pdf->SetProtection(['copy', 'print'], 'nwdi@2eE!3');
								$pdf->Output(public_path('APE/'.$request->code.'/F'.$request->code.'.'.$getEx[1] ), 'F');
							}
							
						}
					}
				}
			      closedir($dh);
			}
		}
		
		
		
		
		
		
		DB::connection('CMS')->table('ResultsStatus')->insertGetId([
			'TransactionNo'		=> $request->code,
			'StatusId'			=> '1',
			'Description'		=> 'Sent, HR can now view on HR Portal',
			//'DateTime'		=> date('Y-m-d H:i:s'),
			'UpdateBy'		=> Auth::user()->username,
			'Action'		=> 'Validated'
		]);
		$return = 'Accepted';
		
		
	}
	else
	{
		$return = 'Error : Transaction already submitted and approved...';
	}
	return $return;
  }   
    
    
    

}
