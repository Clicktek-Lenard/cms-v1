<?php
   
namespace App\Http\Controllers\qrcode;
  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use App\Models\cms\Queue;
use App\Models\eros\ErosDB;

require_once 'BarcodeQRMe.php';
require_once 'wwwPass.php';
class QrcodeController extends Controller
{
   
   public function index()
   {
	/*$qr = new \BarcodeQRMe();
			
	$code = $_GET['code'];
	$from = $_GET['from'];
	$to = $_GET['to'];
	
	for($i = $from; $i <=  $to; $i++)
	{
		//https://assets.nwdi.com/sqrcode/DG-00066
		$ii = sprintf('%05d', $i);
		$qr->url("https://assets.nwdi.com/sqrcode/".$code."-".$ii); 
		$qr->draw(150, "assetQRcode/".$code."-".$ii.".png");	
	
	}
	echo "Done - found at public/assetQRcode";	
	*/
	
	echo $this->wp_hash_password('a2CBD84998@1121');
	
	
   }
   
   function wp_hash_password( $password ) {
		global $wp_hasher;

		if ( empty( $wp_hasher ) ) {
			
			// By default, use the portable hash from phpass.
			$wp_hasher = new \PasswordHash( 8, true );
		}

		return $wp_hasher->HashPassword( trim( $password ) );
	}
   
}