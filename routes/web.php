<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\qrcode\QrcodeController;

use App\Http\Controllers\eros\ErosController;
use App\Http\Controllers\eros\PhysicianController;
use App\Http\Controllers\eros\PhysicianMasterListController;
use App\Http\Controllers\eros\pages\DoctorModalController;
use App\Http\Controllers\eros\pages\PhysicianHistoryController;
use App\Http\Controllers\eros\CompanyController;
use App\Http\Controllers\eros\CompanyCISController;
use App\Http\Controllers\eros\CompanyItemsPackagesController;
use App\Http\Controllers\eros\CompanyPackageController;
use App\Http\Controllers\eros\CompanyItemController;
use App\Http\Controllers\eros\CompanyLab2LabController;
use App\Http\Controllers\eros\ItemController;
use App\Http\Controllers\eros\ItemMasterListController;
use App\Http\Controllers\eros\ItemPriceController;
use App\Http\Controllers\eros\PatientServerController;
use App\Http\Controllers\eros\ItemCodeController;
use App\Http\Controllers\eros\downloadItemCodeController;
use App\Http\Controllers\cms\QueueCISViewController;
/* Physician API data */
use App\Http\Controllers\cms\api\QueuePhysicianController;

/*ZENNYA*/
use App\Http\Controllers\zennya\api\ZennyaController;


use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\ImdApeController;
use App\Http\Controllers\hclab\HclabController;
use App\Http\Controllers\hclab\HclabServerController;
use App\Http\Controllers\hclab\ItemMasterController;

use App\Http\Controllers\ping\PingController;


use App\Http\Controllers\hclab\CheckingController;

use App\Http\Controllers\bizbox\BizboxPatientController;




use App\Http\Controllers\eros\ErosTestController;
use App\Http\Controllers\hl7\ErosHL7Controller;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
$buURI = \Request::segment(1); 

// sample zennya check in
Route::resource('ZennyaCheck', ZennyaController::class);

//Route::resource('ping', PingController::class);

#Route::resource('checking', CheckingController::class);
#######Route::resource('hclab', HclabController::class);
##Route::get('passwordResert', [ErosController::class, 'passwordResert']);
//excel.nwdi.ad


Route::resource('bizboxPatient', BizboxPatientController::class);






Route::get('hclab/list', [HclabServerController::class, 'getPDFList'])->name('hclabserver.PDFlist');
Route::get('eros/list', [PatientServerController::class, 'getPatientList'])->name('erosserver.PatientList');
Route::get('eros/patient', [ErosPatientServerController::class, 'getPatientList'])->name('cmsErosServer.PatientList');

Route::resource('hclab', HclabServerController::class);
Route::resource('itemMaster', ItemMasterController::class);
Route::resource('erosPatientServer', PatientServerController::class);

// CROND TAB 
Route::get('insertErosDB', [ErosController::class, 'insertPatient']);
Route::get('insertBizBoxDB', [ErosController::class, 'insertPatientBizBox']);
Route::get('insertECGPatientBizBox', [ErosController::class, 'insertECGPatientBizBox']);
// not used -- Route::get('erosTransaction', [ErosController::class, 'erosTransaction']);
Route::get('updateItemPrice', [ErosController::class, 'updateItemPrice']);
Route::get('getDataItemPrice', [ErosController::class, 'getDataItemPrice']); //re-get all item price in Eros to CMS
Route::get('getDataErosByDate', [ErosController::class, 'getDataErosByDate']);
Route::get('updatePatientMaster', [ErosController::class, 'updatePatientMaster']);
Route::get('updateItemPriceStatusSync', [ErosController::class, 'updateItemPriceStatusSync']);
Route::get('installRemote', [ErosController::class, 'installRemote']); // to be remove
Route::get('insertPatientEros2BizBox', [ErosController::class, 'insertPatientEros2BizBox']);
Route::get('updateBizBoxDBCorrectApeDate', [ErosController::class, 'bizboxDateCorrection']);
Route::get('airChina', [FileUploadController::class, 'airChinaFileUpload'])->name('airchina.file.upload');
Route::post('airChina', [FileUploadController::class, 'airChinaFileUploadPost'])->name('airchina.file.upload.post');



Route::get('imdAPE', [ImdApeController::class, 'imdApeFileUpload'])->name('imdApe.file.upload');
Route::post('imdAPE', [ImdApeController::class, 'imdApeFileUploadPost'])->name('imdApe.file.upload.post');

// CEBU TAB START
Route::get('CebuCompanyUpdate', [ErosController::class, 'CebuCompanyUpdate']);
Route::get('CebuItemPriceUpdate', [ErosController::class, 'CebuItemPriceUpdate']);
Route::get('CebuPhysicianUpdate', [ErosController::class, 'CebuPhysicianUpdate']);


// CEBU TAB END


Route::get('updateMyPicture', [ErosController::class, 'updateMyPicture']);

// END CROND

// HL7 creator
Route::get('insertBillingHead', [ErosTestController::class, 'insertBillingHead']);
Route::get('erosMakeHL7File', [ErosHL7Controller::class, 'erosMakeHL7File']);


//QR Code
Route::resource('assetQRcode', QrcodeController::class); 



#Route::get('hclab', [HclabController::class, 'index']);
#Route::get('getPDFview', [HclabController::class, 'getPDFview']);

// EROS need to update
Route::resource('eros', ErosController::class); // index Air China JotForms
// Route::get('updatePhysician', [ErosController::class, 'updatePhysician']);
Route::get('insertPhysician', [ErosController::class, 'insertPhysician']);
Route::get('getPhysician', [ErosController::class, 'getPhysician']);



//Route::group(['middleware' => ['auth'] ], function()
//{

	//switch($_SERVER['HTTP_HOST']): 
	//	case 'excel.nwdi.ad':
			
			
			
			
		
			//Route::get('qrcode', [QrcodeController::class, 'qrcodeFileUpload'])->name('qrcode.file.upload');
			//Route::post('qrcode', [QrcodeController::class, 'qrcodeFileUploadPost'])->name('qrcode.file.upload.post');
			
			
	//	break;
	//	case 'eros.nwdi.ad':
			
			
			
			
			Route::resource('item', ItemController::class);
			Route::resource('itemPrice', ItemPriceController::class); // eros
			
	//	break;

	//endswitch;
	
//});

Route::group(['prefix' => 'zennya', 'middleware' => ['auth'] ], function()
{
	Route::resource('api', ZennyaController::class);

});

Route::group(['prefix' => 'cmsphysician', 'middleware' => ['auth'] ], function()
{
	Route::resource('physician', PhysicianController::class);
	Route::resource('doctorsmodule', PhysicianMasterListController::class);
	Route::resource('declinemodal', DoctorModalController::class);
	Route::resource('historyInfo', PhysicianHistoryController::class);
	Route::post('/approvaldoctor', [DoctorModalController::class, 'approvalDoctor'])->name('approvaldoctor');
	Route::post('/declinedoctor', [DoctorModalController::class, 'declineDoctor'])->name('declinedoctor');
	Route::post('api/storePhysicianData/{id}', [QueuePhysicianController::class, 'storePhysicianData'])->name('storePhysicianData');

});

Route::group(['prefix' => 'erosui', 'middleware' => ['auth','CMS'] ], function()
{
	Route::resource('company', CompanyController::class);
	//Route::resource('physician', PhysicianController::class);
	// Route::resource('doctorsmodule', PhysicianMasterListController::class);
	// Route::resource('declinemodal', DoctorModalController::class);
	// Route::post('/approvaldoctor', [DoctorModalController::class, 'approvalDoctor'])->name('approvaldoctor');
	// Route::post('/declinedoctor', [DoctorModalController::class, 'declineDoctor'])->name('declinedoctor');
	
	Route::group(['prefix' => 'company', 'middleware' => ['auth'] ], function()
	{	
		Route::resource('itemspackages', CompanyItemsPackagesController::class);
		Route::resource('cis', CompanyCISController::class);
		Route::resource('cisview', QueueCISViewController::class);
		Route::group(['prefix' => 'itemspackages', 'middleware' => ['auth'] ], function()
		{
			Route::resource('item', CompanyItemController::class);
			Route::resource('package', CompanyPackageController::class);
			Route::resource('lab2lab', CompanyLab2LabController::class);
			Route::group(['prefix' => 'package', 'middleware' => ['auth'] ], function()
			{
				Route::post('editSaveAjax', [CompanyPackageController::class, 'editSaveAjax']);
				Route::post('newSaveAjax', [CompanyPackageController::class, 'newSaveAjax']);
			});
			Route::group(['prefix' => 'item', 'middleware' => ['auth'] ], function()
			{
				Route::post('editSaveAjax', [CompanyItemController::class, 'editSaveAjax']);
				Route::post('newSaveAjax', [CompanyItemController::class, 'newSaveAjax']);
			});
			
		});
		
	});
});








Route::get('/', function () {
    return view('app');
})->middleware(['auth'])->name('CMS');

require __DIR__.'/auth.php';



