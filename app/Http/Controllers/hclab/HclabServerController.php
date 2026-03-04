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

class HclabServerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

   
    public function index()
    {
	//dispatch(new STS\Tunneler\Jobs\CreateTunnel());
	
	//return DB::connection('WWW')->select("SELECT * FROM pdf_results LIMIT 1000 ");
	
	//$pdfData = HclabDB::getPDFDataServer();
        //return view('hclab.pdfListServer', ['pdfData' => $pdfData]);
	return view('hclab.pdfListServer');
    }
    
    public function getPDFList(Request $request)
    {
	//die();
	
	$search_arr = $request->get('search');
	$searchValue = $search_arr['value']; // Search value
	//echo HclabDB::getPDFDataServer(  $searchValue  );
	//die();
	if ($request->ajax()) {
	
		$model = WWWPDF::getPDFDataServer($searchValue);

		return DataTables::of($model)
                //->only(['id','FullName', 'patient_id', 'birthdate', 'or_no', 'trans_no', 'created_at', 'FullPDF'])
                ->toJson();
	
	
            //return Datatables::of(HclabDB::getPDFDataServer(  $searchValue  ))
                //->addIndexColumn()
                //->addColumn('action', function($row){
                //    $actionBtn = '<a href="javascript:void(0)" class="edit btn btn-success btn-sm">Edit</a> <a href="javascript:void(0)" class="delete btn btn-danger btn-sm">Delete</a>';
                 //   return $actionBtn;
                //})
                //->rawColumns(['action'])
              //  ->make(true);
        }
	
	//return view('hclab.pdfListServer', ['pdfData' => json_encode(HclabDB::getPDFDataServer())]);
	
	
    }
    
    public function show($id)
    {

	$adapter = new \League\Flysystem\AwsS3V3\AwsS3V3Adapter(
		new \Aws\S3\S3Client([
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY')
            ],
            'region' => 'ap-southeast-1',
            'version' => 'latest',

        ]),
		'nwdi-pdf-bucket',
		'',
		new \League\Flysystem\AwsS3V3\PortableVisibilityConverter(
			\League\Flysystem\Visibility::PRIVATE
		)
	);
	
	$s3Fs = new \League\Flysystem\Filesystem($adapter);
    
	
	$pdfData = WWWPDF::getPDFidDataServer($id);
	
	
	$fileName = $pdfData[0]->trans_no . '_' . date('Ymd', strtotime($pdfData[0]->order_date)) . '_' . $pdfData[0]->patient_id . '.pdf';	
	$fullPath =   date('Y-m-d', strtotime($pdfData[0]->order_date)) . '/' . $fileName;
	
	if (!$s3Fs->fileExists($fullPath)) {
		echo '<div style="background-color: #10069f; padding: 20px 30px; margin: 200px auto; width: 400px;"><p style="color: #fff; margin: auto; font-family: Roboto, sans-serif; text-align: center; font-weight: bold;">Test still in process..</p></div>';
		exit;
	}
	
	  $disposition =  'inline';

	    header('Content-Type: application/pdf');
	    header('Content-Transfer-Encoding: Binary');
	    header('Content-disposition: ' . $disposition . '; filename="' . $fileName . '"');
	    echo $s3Fs->read($fullPath);	
	    exit;
    
	
    }

}
