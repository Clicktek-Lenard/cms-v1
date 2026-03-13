<?php
namespace App\Http\Controllers\hclab;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Http\Controllers\Controller;
use App\Models\www\WWWPDF;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;

use DataTables;

class CheckingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

   
    public function index()
    {
    
	$adapter = new \League\Flysystem\AwsS3V3\AwsS3V3Adapter(
		new \Aws\S3\S3Client([
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY')
            ],
            'region' => env('AWS_DEFAULT_REGION', 'ap-southeast-1'),
            'version' => 'latest',

        ]),
		env('AWS_BUCKET', 'nwdi-pdf-bucket'),
		'',
		new \League\Flysystem\AwsS3V3\PortableVisibilityConverter(
			\League\Flysystem\Visibility::PRIVATE
		)
	);
	
	$s3Fs = new \League\Flysystem\Filesystem($adapter);
    
	
	$pdfDatas = DB::connection('hclab')->select(" SELECT * FROM trans_eros WHERE	 `FileStatus` IS NULL ");
	
	$i = 0;
	foreach($pdfDatas as $pdfData )
	{
		
		
		$fileName = $pdfData->TransactionNo . '_' . date('Ymd', strtotime($pdfData->Date)) . '_' . $pdfData->PatientId . '.pdf';	
		$fullPath =   date('Y-m-d', strtotime($pdfData->Date)) . '/' . $fileName;
		
		if ($s3Fs->fileExists($fullPath)) {
			 DB::connection('hclab')->update(" UPDATE  `trans_eros` SET `FileStatus` = 'FOUND' where `id` = '".$pdfData->Id."'  ");
		}else{
			 DB::connection('hclab')->update(" UPDATE  `trans_eros` SET `FileStatus` = 'NOT FOUND' where `id` = '".$pdfData->Id."'  ");
		}
	}
	  
	echo 'Total Count not Exist - '. $i;


	


    }
    
    public function getPDFList(Request $request)
    {
	
	
    }
    
    public function show($id)
    {

	
	
    }

}
