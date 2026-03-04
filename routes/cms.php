<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



/*CMS*/
use App\Http\Controllers\cms\QueueController;
use App\Http\Controllers\cms\PastQueueController;
use App\Http\Controllers\cms\PastQueueOnSiteController;
use App\Http\Controllers\cms\BranchManagerModuleController;
use App\Http\Controllers\cms\CardVerificationController;


use App\Http\Controllers\cms\doctors\DoctorController;
use App\Http\Controllers\cms\doctors\DoctorsCompleteQueueController;
use App\Http\Controllers\cms\doctors\DoctorTempController;
use App\Http\Controllers\cms\doctors\DoctorsConsultationHistoryController;
use App\Http\Controllers\cms\doctors\MedicalEvaluationController;
use App\Http\Controllers\cms\doctors\doctorModals\MedicalEvaluationModalController;
use App\Http\Controllers\cms\doctors\doctorModals\VitalHistoryModalController;
use App\Http\Controllers\cms\doctors\doctorModals\DoctorsOrderModalController;
use App\Http\Controllers\cms\doctors\doctorModals\ModalsController;
use App\Http\Controllers\cms\doctors\doctorModals\PcpDoctorModalController;
use App\Http\Controllers\cms\doctors\PEController;
use App\Http\Controllers\cms\doctors\MedicalEvaluationCompanyController;
use App\Http\Controllers\cms\doctors\MedicalEvaluationPatientController;

use App\Http\Controllers\cms\api\AssessmentController;

use App\Http\Controllers\cms\ECardRegistrationController;
use App\Http\Controllers\cms\ECardReceivingController;
use App\Http\Controllers\cms\ECardReceivedController;
use App\Http\Controllers\cms\ECardTransferController;
use App\Http\Controllers\cms\pages\enrollment\EnrollmentReceivedController;
use App\Http\Controllers\cms\pages\enrollment\EnrollmentTransferController;
use App\Http\Controllers\cms\UserHclabController;
use App\Http\Controllers\cms\api\QueuePatientController;
use App\Http\Controllers\cms\api\QueuePhysicianController;
use App\Http\Controllers\cms\api\QueueItemPriceController;
use App\Http\Controllers\cms\api\TransactionEditPriceController;
use App\Http\Controllers\cms\api\CityController;
use App\Http\Controllers\cms\api\VPNController;
use App\Http\Controllers\cms\api\ZipController;
use App\Http\Controllers\cms\pages\queue\QueuePagePatientController;
use App\Http\Controllers\cms\pages\queue\QueuePageTransactionTempController;
use App\Http\Controllers\cms\pages\queue\QueuePageScanTransactionTempController;
use App\Http\Controllers\cms\pages\queue\QueueCardValidationController;
use App\Http\Controllers\cms\pages\queue\QueuePagePackageInclusionController;
use App\Http\Controllers\cms\pages\queue\EditOrController;
use App\Http\Controllers\cms\pages\queue\QueueResendHL7; //Resend HL7
use App\Http\Controllers\cms\pages\queue\CancelTransactionController;
use App\Http\Controllers\cms\pages\queue\OutsidePhysicianController;
use App\Http\Controllers\cms\pages\queue\PhysicianTableController;
use App\Http\Controllers\cms\pages\queue\PhysicianApprovalController;
use App\Http\Controllers\cms\pages\queue\PhysicianDeclineController;
use App\Http\Controllers\cms\pages\queue\PhysicianEditInformationController;
use App\Http\Controllers\cms\pages\queue\TransactionController;
use App\Http\Controllers\cms\pages\queue\AnteDateController;
use App\Http\Controllers\cms\pages\queue\EditTransactionController;
use App\Http\Controllers\cms\AdjustedQueueController;
use App\Http\Controllers\cms\pages\queue\QueueDeletedTransactionController; //for view delete transaction
use App\Http\Controllers\cms\pages\queue\QueueDisplayController;
use App\Http\Controllers\cms\ServicesController;
use App\Http\Controllers\cms\PaymentController;
use App\Http\Controllers\cms\PastPaymentController;
//use App\Http\Controllers\cms\ResultUploadingController;
use App\Http\Controllers\cms\pages\payment\PaymentPageTransactionsController;
use App\Http\Controllers\cms\pages\payment\SummaryPageTransactionsController;
use App\Http\Controllers\cms\pages\payment\BankNamePageTransactionsController;
//use App\Http\Controllers\cms\SendoutController;
use App\Http\Controllers\cms\RpLeadReportController; //RP LEAD REPORT
use App\Http\Controllers\cms\SendoutreportsController;
use App\Http\Controllers\cms\HmoCorporateReportsController;
use App\Http\Controllers\cms\TurnaroundTimeController;
use App\Http\Controllers\cms\CardDemographicsController;
use App\Http\Controllers\cms\WorkstationController;

//QUEUEING
use App\Http\Controllers\cms\queuing\ReceptionQueueController;
use App\Http\Controllers\cms\queuing\LaboratoryQueueController;
use App\Http\Controllers\cms\queuing\ImagingQueueController;
use App\Http\Controllers\cms\queuing\ConsultationQueueController;
use App\Http\Controllers\cms\queuing\ReleasingQueueController;
use App\Http\Controllers\cms\queuing\VitalSignsQueueController;
use App\Http\Controllers\cms\pages\queue\SendOutReceivingController;
use App\Http\Controllers\cms\pages\queue\SendOutController;

use App\Http\Controllers\cms\receiving\SpecimenReceivingController;
use App\Http\Controllers\cms\receiving\TransportController;
use App\Http\Controllers\cms\receiving\LaboratoryReceivingController;

use App\Http\Controllers\hl7\QueuingEventsController;
//APE IMD


use App\Http\Controllers\cms\WebCamController;
use App\Http\Controllers\cms\PhysicianWebcamController;


// to be confirm
use App\Http\Controllers\cms\settings\UsersController;
use App\Http\Controllers\cms\CardNumberGenController;

use App\Http\Controllers\cms\VerifiedNumbersController;

use  App\Http\Controllers\cms\api\hPDFController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* WebCam */
	Route::controller(WebCamController::class)->group(function () {
	    Route::get('webcam', 'index')->name('webcam.capture');
	    Route::post('webcam', 'store');
	});
	Route::controller(PhysicianWebcamController::class)->group(function () {
	    Route::get('physicianWebcam', 'index')->name('physicianWebcam.capture');
	    Route::post('physicianWebcam', 'store');
	});
/* Enrollment */

/* For Attending Physician  */
Route::get('/api/physicians', [PcpDoctorModalController::class, 'getPhysicians']);
/* End For Attending Physician  */

Route::resource('vpn', VPNController::class);

Route::group(['prefix' => 'enrollment', 'middleware' => ['auth', 'CMS'] ], function()
{

	Route::post('/cardverified', 'VerifiedNumbersController@cardverified');
	//Route::resource('cardverification', CardVerificationController::class);
	Route::resource('cardverified', VerifiedNumbersController::class);

	Route::resource('cardregistration', ECardRegistrationController::class);
	Route::resource('cardreceiving', ECardReceivingController::class);
	Route::resource('cardreceived', ECardReceivedController::class);
	Route::resource('cardtransfer', ECardTransferController::class);

	Route::group(['prefix' => 'pages', 'middleware' => ['auth', 'CMS'] ], function()
	{
		Route::resource('enrollmentReceived', EnrollmentReceivedController::class);
		Route::resource('enrollmentTransfer', EnrollmentTransferController::class);
	});	

	Route::post('/cardnumber/store', [CardNumberGenController::class, 'store'])->name('cardnumber.store');
	Route::get('/cardnumber/get-last-series-number', [CardNumberGenController::class, 'getLastSeriesNumber'])->name('cardnumber.getLastSeriesNumber');
	Route::get('/cardnumber/generate-barcode/{cardNumber}', [CardNumberGenController::class, 'generateBarcode'])->name('generate.barcode');
	Route::get('/cardnumber/get-last-batch', [CardNumberGenController::class, 'getLastBatch'])->name('cardnumber.getLastBatch');
	Route::resource('cardnumber', CardNumberGenController::class);


});
/* End of Enrollment */
Route::group(['prefix' => 'doctor', 'middleware' => ['auth', 'CMS'] ], function()
{
	
	Route::post('/vitals/doctordecking/pcpdoctor',[ModalsController::class,'PcpModal']);
	Route::get('/queue/order/orderModal/{id}/edit',[ModalsController::class, 'EditOrderModal']);
	Route::post('/queue/order/orderModal',[ModalsController::class, 'SaveOrderModal']);
	Route::get('/queue/pastResult/{id}',[ModalsController::class, 'PastResult']);


	Route::resource('vitals', 'App\Http\Controllers\cms\doctors\VitalsController');
	Route::resource('historyqueue', DoctorsCompleteQueueController::class);
	Route::resource('queue', DoctorController::class);
	Route::resource('pe', PEController::class);
	
	
	
	Route::group(['prefix' => 'vitals', 'middleware' => ['auth', 'CMS'] ], function()
	{
		Route::group(['prefix' => 'doctordecking', 'middleware' => ['auth', 'CMS'] ], function()
		{
			Route::resource('pcpdoctor', PcpDoctorModalController::class);
		});
	});
	Route::group(['prefix' => 'evaluation', 'middleware' => ['auth', 'CMS'] ], function()
	{
		Route::get('getresultcompany', ['App\Http\Controllers\cms\doctors\MedicalEvaluationController', 'getCompanyList'])->name('cmsResultCompany.CompanyList');
		Route::group(['prefix' => 'evalModal', 'middleware' => ['auth', 'CMS'] ], function()
		{
			Route::resource('evalModalView', MedicalEvaluationModalController::class);
		});
		Route::group(['prefix' => 'company', 'middleware' => ['auth', 'CMS'] ], function()
		{
			Route::get('getresultcompanymonitoring', ['App\Http\Controllers\cms\doctors\MedicalEvaluationCompanyController', 'getPatientList'])->name('cmsResultCompanyMonitoring.PatientList');
			Route::resource('patient', MedicalEvaluationPatientController::class);
		});
		Route::resource('company', MedicalEvaluationCompanyController::class);
	});
	Route::resource('evaluation', MedicalEvaluationController::class);
	Route::group(['prefix' => 'queue', 'middleware' => ['auth', 'CMS'] ], function()
	{
		Route::group(['prefix' => 'draft', 'middleware' => ['auth', 'CMS'] ], function()
		{
			Route::resource('temp', DoctorTempController::class);
		});
		
		
	});
	
	//Route::group(['prefix' => 'doctor', 'middleware' => ['auth', 'CMS'] ], function()
	//{
		Route::group(['prefix' => 'past', 'middleware' => ['auth', 'CMS'] ], function()
		{
			Route::resource('history', DoctorsConsultationHistoryController::class);
			//Route::resource('vitalhistory', VitalHistoryModalController::class);
		});
	//});
});
Route::group(['prefix' => 'processing', 'middleware' => ['auth', 'CMS'] ], function()
{
	Route::group(['prefix' => 'evaluated', 'middleware' => ['auth', 'CMS'] ], function()
	{
		Route::get('getevaluatedcompany', ['App\Http\Controllers\cms\doctors\EvaluatedController', 'getCompanyList'])->name('cmsEvaluatedCompany.CompanyList');
		Route::group(['prefix' => 'company', 'middleware' => ['auth', 'CMS'] ], function()
		{
			Route::get('getevaluatedcompanypatient', ['App\Http\Controllers\cms\doctors\EvaluatedCompanyController', 'getPatientList'])->name('cmsEvaluatedCompany.PatientList');
			Route::resource('patient', 'App\Http\Controllers\cms\doctors\EvaluatedPatientController');
		});
		Route::resource('company', 'App\Http\Controllers\cms\doctors\EvaluatedCompanyController');
	});
	Route::resource('evaluated', 'App\Http\Controllers\cms\doctors\EvaluatedController');

});
Route::group(['prefix' => 'cms', 'middleware' => ['auth', 'CMS'] ], function()
{
	//
	
	//Route::resource('resultcompany', 'App\Http\Controllers\cms\ResultCompanyController');
	
	Route::resource('resultsmonitoring', 'App\Http\Controllers\cms\ResultsMonitoringController');
	Route::get('getresultsmonitoring', ['App\Http\Controllers\cms\ResultsMonitoringController', 'getPatientList'])->name('cmsResultsMonitoring.PatientList');
	Route::group(['prefix' => 'queue', 'middleware' => ['auth', 'CMS'] ], function()
	{
		Route::post('validate', [QueueCardValidationController::class, 'checkCardNumber']);
		Route::post('discount', [QueueCardValidationController::class, 'discountValidation']);
		Route::resource('Package', QueuePagePackageInclusionController::class);
		Route::resource('anteDateTransaction', AnteDateController::class);
		Route::resource('outsidePhysician', OutsidePhysicianController::class);      
		Route::resource('physicianInfo', PhysicianTableController::class);
		Route::resource('physicianInfoEdit', PhysicianEditInformationController::class);
		Route::resource('physicianInfoView', PhysicianApprovalController::class);
		Route::resource('physicianDeclineModal', PhysicianDeclineController::class);
		Route::post('/physician-update', [PhysicianTableController::class, 'physicianupdate'])->name('physicianupdate');
		Route::post('/physician-edit', [PhysicianEditInformationController::class, 'editphysician'])->name('editphysician');
		Route::post('/physician-approval', [PhysicianApprovalController::class, 'physicianApproval'])->name('physicianapproval');
		Route::post('/physician-decline', [PhysicianDeclineController::class, 'physicianDecline'])->name('physiciandecline');
		Route::get('search', [OutsidePhysicianController::class, 'searchPhysician'])->name('outsidePhysiciansearch');
		Route::post('/approval-transaction', [EditTransactionController::class, 'approvalTransaction'])->name('approvaltransaction');				
		Route::post('IdItemPrice',[ QueuePagePackageInclusionController::class, 'edit']);
		Route::get('Package/{id}/{room}', [QueuePagePackageInclusionController::class, 'room']);
		Route::post('api/itemPrice', [QueueItemPriceController::class, 'getItemPrice']);
		Route::post('api/transactionItemPrice', [TransactionEditPriceController::class, 'getTransactionItemPrice']);
		Route::get('api/getPhysicianName', [QueuePhysicianController::class, 'getPhysicianName'])->name('getPhysicianName');
		Route::post('resendHL7', [QueueResendHL7::class, 'resendHL7']);
		Route::post('pastresendHL7', [QueueResendHL7::class, 'pastresendHL7']);

		Route::get('company/data', [QueuePageTransactionTempController::class, 'getCompanyData'])->name('company.data');

		Route::post('/resend-hl7', [QueuePagePackageInclusionController::class, 'writeHL7MessageRequest'])->name('hl7.resend');	
	});
	Route::resource('resultuploading', 'App\Http\Controllers\cms\ErosPatientServerController');
	Route::get('eros/patient', ['App\Http\Controllers\cms\ErosPatientServerController', 'getPatientList'])->name('cmsErosServer.PatientList');
	
	Route::get('dropzone/getfiles', 'App\Http\Controllers\cms\ErosPatientServerController@getFiles');
	Route::post('dropzone/deletefile', 'App\Http\Controllers\cms\ErosPatientServerController@deleteFile');
	Route::post('dropzone/store', 'App\Http\Controllers\cms\ErosPatientServerController@dropzoneStore')->name('dropzone.store');
	Route::post('dropzone/updatefile', 'App\Http\Controllers\cms\ErosPatientServerController@updateFile');
	
	Route::group(['prefix' => 'doctor', 'middleware' => ['auth', 'CMS'] ], function()
	{
		Route::group(['prefix' => 'past', 'middleware' => ['auth', 'CMS'] ], function()
		{
			//Route::resource('history', DoctorsConsultationHistoryController::class);
			Route::resource('vitalhistory', VitalHistoryModalController::class);
		});
	});
	
	Route::resource('queue', QueueController::class);
	
	Route::get('pastqueue/list', [PastQueueController::class, 'getList'])->name('pastqueue.getList');
	Route::resource('pastqueue', PastQueueController::class);
	Route::get('pastqueueonsite/list', [PastQueueOnSiteController::class, 'getList'])->name('pastqueueonsite.getList');
	Route::resource('pastqueueonsite', PastQueueOnSiteController::class);
	Route::resource('bmmodule', BranchManagerModuleController::class);  //12-02-24 added

	Route::group(['prefix' => 'pastqueue', 'middleware' => ['auth', 'CMS'] ], function()
	{
		Route::resource('EditOr', EditOrController::class);
		Route::resource('cancelTransaction', CancelTransactionController::class);
		Route::group(['prefix' => 'pages', 'middleware' => ['auth', 'CMS'] ], function()
		{
			Route::resource('pastQueueCancel', CancelQueueController::class);
		});

		Route::resource('deletedTransaction', QueueDeletedTransactionController::class);  //for view delete transaction
	});
		
	Route::group(['prefix' => 'queue', 'middleware' => ['auth', 'CMS'] ], function()
	{
		Route::group(['prefix' => 'api', 'middleware' => ['auth', 'CMS'] ], function()
		{
			Route::resource('getPatientName', QueuePatientController::class);
			Route::resource('getPhysicianName', QueuePhysicianController::class);
			Route::resource('itemPrice', QueueItemPriceController::class);
			Route::resource('transactionItemPrice', TransactionEditPriceController::class);			
			Route::resource('city', CityController::class);
			Route::resource('zip', ZipController::class);
			//Route::resource('patientName', 'cms\api\PatientController');
			
		});
		Route::group(['prefix' => 'pages', 'middleware' => ['auth', 'CMS'] ], function()
		{
			Route::resource('queuePatient', QueuePagePatientController::class);
			Route::resource('physicianEnrollment', PhysicianTableController::class);
			Route::post('/physician-insert', [PhysicianTableController::class, 'insertPhysician'])->name('physicianinsert');
			Route::resource('transactionTemp', QueuePageTransactionTempController::class);
			Route::resource('scanner', QueuePageScanTransactionTempController::class);
		});
		
		route::post('/re-gerenate-pdf', [hPDFController::class, 'reGeneratePDF'])->name('regeneratepdf');
		
	});
	Route::resource('payment', PaymentController::class);
	Route::resource('pastpayment', PastPaymentController::class);
	Route::group(['prefix' => 'pastpayment', 'middleware' => ['auth', 'CMS'] ], function(){
		Route::post('/approval-transaction', [EditTransactionController::class, 'approvalTransaction'])->name('approvaltransaction');
	});
	Route::group(['prefix' => 'payment', 'middleware' => ['auth', 'CMS'] ], function()
	{
		Route::group(['prefix' => 'pages', 'middleware' => ['auth', 'CMS'] ], function()
		{
			Route::resource('transactions', PaymentPageTransactionsController::class);
			Route::resource('summary', SummaryPageTransactionsController::class);
			Route::resource('bankname', BankNamePageTransactionsController::class);
			Route::resource('agent', AgentEmpNamePageTransactionsController::class);
		});
	
	});


	//Route::resource('nurse', 'cms\NurseController'); not use for now
	//Route::resource('doctor', 'cms\DoctorController'); not use for now

	Route::resource('lab', 'cms\LaboratoryController');
	Route::resource('rad', 'cms\RadiologyController');
	Route::group(['prefix' => 'pages', 'middleware' => ['auth', 'CMS'] ], function()
	{	
		Route::resource('transaction', 'App\Http\Controllers\cms\TransactionController');   // used this for remove transactions  //uri = /cms/pages/transaction 
		Route::resource('editTransaction', 'App\Http\Controllers\cms\pages\queue\EditTransactionController');
		//Route::post('/approval-transaction', [EditTransactionController::class, 'approvalTransaction'])->name('approvaltransaction');
		Route::resource('patient', 'cms\PatientController'); // to be check
		Route::resource('vitals', 'cms\VitalsController'); // to be check
	});
	
	Route::group(['prefix' => 'settings', 'middleware' => ['auth'] ], function()
	{	
		Route::resource('users', UsersController::class);
		
		Route::group(['prefix' => 'pages', 'middleware' => ['auth', 'CMS.settings'] ], function()
		{
			Route::resource('users', UsersController::class);
		});
	});

	Route::resource('workstation', WorkstationController::class);
	Route::resource('displayqueue', QueueDisplayController::class);
	Route::get('/services/ping', [ServicesController::class, 'ping'])->name('services.ping');
	Route::get('/services/jasper', [ServicesController::class, 'jasperAction'])->name('services.jasper');
	Route::get('/services/socket', [ServicesController::class, 'runSocket'])->name('services.socket');
	Route::get('/services/hl7', [ServicesController::class, 'runHl7'])->name('services.hl7');
	Route::get('/services/sql', [ServicesController::class, 'checkSql'])->name('services.sql');

	Route::resource('services', ServicesController::class);

	Route::resource('sendout-receiving', SendOutReceivingController::class);
	Route::post('sendout/receive-specimen', [SendOutReceivingController::class, 'receiveSendoutSpecimen'])->name('receivesendoutspecimen');

	Route::resource('sendout', SendOutController::class);

	
	Route::group(['prefix' => 'api', 'middleware' => ['auth'] ], function()
	{
		Route::resource('hpdf',  'App\Http\Controllers\cms\api\hPDFController');
		Route::resource('getAssessmentName', AssessmentController::class);
	
	});

	Route::get('laboratory-receiving/fetch', [LaboratoryReceivingController::class, 'fetch'])->name('laboratory.receiving.fetch');
	Route::post('laboratory-receiving/receive', [LaboratoryReceivingController::class, 'receive'])->name('laboratory.receiving.receive');
	Route::resource('laboratory-receiving', LaboratoryReceivingController::class);

});

Route::group(['prefix' => 'specimen-receiving', 'middleware' => ['auth', 'CMS']], function () 
{
	Route::get('specimen', [SpecimenReceivingController::class, 'indexSpecimenReceiving'])->name('specimen.index');
    Route::get('bloodextraction', [SpecimenReceivingController::class, 'indexBloodExtraction'])->name('bloodextraction.index');
	Route::get('imaging', [SpecimenReceivingController::class, 'indexImaging'])->name('imaging.index');

    Route::get('specimen/{id}/edit', [SpecimenReceivingController::class, 'edit'])->name('specimen.edit');
	Route::post('specimen/{id}/receive', [SpecimenReceivingController::class, 'receiveSpecimen'])->name('specimen.receive');

	Route::resource('transport', TransportController::class);
	Route::post('/transport/generate-word', [TransportController::class, 'generateWordDoc'])->name('transport.generate.word');

	Route::get('rejection', [TransportController::class, 'rejectionIndex'])->name('rejection.index');
	Route::post('/rejection/reject-specimen', [TransportController::class, 'rejectSpecimen'])->name('reject.specimen');

	Route::get('rejected', [TransportController::class, 'rejectedIndex'])->name('rejected.index');
	Route::post('/rejected/receive-specimen', [TransportController::class, 'receiveSpecimen'])->name('receive.specimen');


	Route::get('specimen/{id}/editImaging', [SpecimenReceivingController::class, 'editImaging'])->name('specimen.editImaging');
});

//QUE DISPLAY
Route::get('queueDisplay', [QueueDisplayController::class, 'display'])->name('queueDisplay.index');
Route::get('/fetch-queue-data', [QueueDisplayController::class, 'fetchQueueData'])->name('fetchqueuedata');
Route::get('/fetch-display-data', [QueueDisplayController::class, 'fetchDisplayData'])->name('fetchdisplaydata');

Route::group(['prefix' => 'reports', 'middleware' => ['auth', 'CMS'] ], function()
{
	Route::resource('dailysales', 'App\Http\Controllers\cms\ReportsController');
	Route::resource('labreports', 'App\Http\Controllers\cms\ReportsLABController');
	Route::resource('sendout', SendoutreportsController::class);
	Route::resource('hmo', HmoCorporateReportsController::class);
	Route::resource('turnaroundtime', TurnaroundTimeController::class);
});
Route::group(['prefix' => 'user', 'middleware' => ['auth', 'CMS'] ], function(){
	Route::resource('hclab', UserHclabController::class);
});

Route::get('rpleadreport', [RpLeadReportController::class, 'rpleadreport']);

Route::group(['prefix' => 'card', 'middleware' => ['auth', 'CMS'] ], function()
{
	Route::resource('demographics', 'App\Http\Controllers\cms\CardDemographicsController');
	
});

Route::group(['prefix' => 'kiosk', 'middleware' => ['auth', 'CMS'] ], function()
{
	// Route::resource('/error', ReceptionQueueController::class);
	Route::group(['prefix' => 'receptionqueue', 'middleware' => ['auth', 'CMS'] ], function()
	{
		Route::resource('/', ReceptionQueueController::class);
		Route::post('/update-status', [ReceptionQueueController::class, 'updateStatus'])->name('updatestatus');
		Route::post('/insert-patient-code', [ReceptionQueueController::class, 'insertPatientCode'])->name('insertpatientcode');
		Route::post('/update-current-room', [ReceptionQueueController::class, 'updateCurrentRoom'])->name('updatecurrentroom');
		Route::get('/queue-data', [ReceptionQueueController::class, 'getQueueData'])->name('getqueuedata');
		Route::get('/queuevitals-data', [ReceptionQueueController::class, 'getQueueVitalsData'])->name('getqueuevitalsdata');
		Route::get('/queueconsultation-data', [ReceptionQueueController::class, 'getQueueConsultationData'])->name('getqueueconsultationdata');
		Route::get('/queue-imaging-data', [ReceptionQueueController::class, 'getQueueImagingData'])->name('getqueueimagingdata');
		Route::get('/queue-data-paid', [ReceptionQueueController::class, 'getQueueDataPaid'])->name('getqueuedatapaid');
		Route::post('/log-action', [ReceptionQueueController::class, 'logAction'])->name('actionlog');
		Route::post('/exit-station', [ReceptionQueueController::class, 'exitStation'])->name('exitstation');
		Route::post('/vital-to-consultation', [ReceptionQueueController::class, 'vitalToConsult'])->name('vitaltoconsult');

	});

	Route::resource('extractionqueue', LaboratoryQueueController::class);
	Route::post('extractionqueue/receive-specimen', [LaboratoryQueueController::class, 'receiveSpecimen'])->name('receivespecimen');
	Route::post('extractionqueue/hold-receiving-rooms', [LaboratoryQueueController::class, 'holdReceivingRooms'])->name('holdreceivingrooms');

	Route::resource('sendout', SendOutReceivingController::class);

	Route::resource('imagingqueue', ImagingQueueController::class);

	Route::resource('consultationqueue', ConsultationQueueController::class);

	Route::resource('vitalsignsqueue', VitalSignsQueueController::class);

	Route::group(['prefix' => 'releasingqueue', 'middleware' => ['auth', 'CMS'] ], function()
	{
		Route::resource('/', ReleasingQueueController::class);
		Route::post('/releasing-exitqueue', [ReleasingQueueController::class, 'exitQueue'])->name('exitqueue');
	});
	
	Route::resource('queue', QueueController::class);
});

Route::get('QueueEvent', [QueuingEventsController::class, 'reset']);

