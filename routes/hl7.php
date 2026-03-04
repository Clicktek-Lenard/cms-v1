<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\hl7\BAEErosHL7Controller;
use App\Http\Controllers\hl7\GHLErosHL7Controller;
use App\Http\Controllers\hl7\HOMErosHL7Controller;
use App\Http\Controllers\hl7\IMDErosHL7Controller;

use App\Http\Controllers\hl7\MAKErosHL7Controller;

use App\Http\Controllers\hl7\PCSErosHL7Controller;
use App\Http\Controllers\hl7\TARErosHL7Controller;

use App\Http\Controllers\hl7\BAEHL7Controller;
use App\Http\Controllers\hl7\IMDHL7Controller;
use App\Http\Controllers\hl7\HOMHL7Controller;

use App\Http\Controllers\hl7\ICTHL7Controller;
use App\Http\Controllers\hl7\MEDHL7Controller;


//use App\Http\Controllers\hl7\MEDHL7Controller;


//Route::get('reUpdateDateFromCMS2Eros', [ErosHL7Controller::class, 'reUpdateDateFromCMS2Eros']);


// HL7 from Eros
//Route::get('ErosForHL7', [ErosHL7Controller::class, 'ErosForHL7']);

Route::get('ICTHL7', [ICTHL7Controller::class, 'HL7']);
Route::get('ICTcmsMakeHL7File', [ICTHL7Controller::class, 'cmsMakeHL7File']);

// Route::get('MEDHL7', [MEDHL7Controller::class, 'HL7']);
// Route::get('MEDcmsMakeHL7File', [MEDHL7Controller::class, 'cmsMakeHL7File']);
// Route::get('MEDupdateCMSHL7QueueStatus', [MEDHL7Controller::class, 'updateCMSHL7QueueStatus']);
// Route::get('MEDUpdateForPaymentQueue', [MEDHL7Controller::class, 'UpdateForPaymentQueue']);
Route::get('MEDHL7', [MEDHL7Controller::class, 'HL7']);
Route::get('MEDcmsMakeHL7File', [MEDHL7Controller::class, 'cmsMakeHL7File']);
Route::get('MEDupdateCMSHL7QueueStatus', [MEDHL7Controller::class, 'updateCMSHL7QueueStatus']);
Route::get('MEDUpdateForPaymentQueue', [MEDHL7Controller::class, 'UpdateForPaymentQueue']);



Route::get('BAEHL7', [BAEHL7Controller::class, 'HL7']);
Route::get('BAEcmsCAHL7File', [BAEHL7Controller::class, 'cmsCAHL7File']);
Route::get('BAEcmsMakeHL7File', [BAEHL7Controller::class, 'cmsMakeHL7File']);
Route::get('BAEupdateCMSHL7QueueStatus', [BAEHL7Controller::class, 'updateCMSHL7QueueStatus']);
Route::get('BAEUpdateForPaymentQueue', [BAEHL7Controller::class, 'UpdateForPaymentQueue']);
Route::get('BAEUpdateConsultationPackage', [BAEHL7Controller::class, 'UpdateConsultationPackage']);
Route::get('BAEmakeAccessionNo', [BAEHL7Controller::class, 'makeAccessionNo']);
Route::get('BAEcmsMakeHL7360', [BAEHL7Controller::class, 'cmsMakeHL7360']);

Route::get('IMDHL7', [IMDHL7Controller::class, 'HL7']);
Route::get('IMDcmsMakeHL7File', [IMDHL7Controller::class, 'cmsMakeHL7File']);
Route::get('IMDupdateCMSHL7QueueStatus', [IMDHL7Controller::class, 'updateCMSHL7QueueStatus']);
Route::get('IMDUpdateForPaymentQueue', [IMDHL7Controller::class, 'UpdateForPaymentQueue']);


Route::get('HOMHL7', [HOMHL7Controller::class, 'HL7']);
Route::get('HOMcmsMakeHL7File', [HOMHL7Controller::class, 'cmsMakeHL7File']);
Route::get('HOMupdateCMSHL7QueueStatus', [HOMHL7Controller::class, 'updateCMSHL7QueueStatus']);
Route::get('HOMUpdateForPaymentQueue', [HOMHL7Controller::class, 'UpdateForPaymentQueue']);



Route::get('HCLABUpdateEmail', [BAEErosHL7Controller::class, 'HCLABUpdateEmail']);



//Route::get('BAEErosForHL7', [BAEErosHL7Controller::class, 'ErosForHL7']);
Route::get('BAECMSQueueUpdateForPayment', [BAEErosHL7Controller::class, 'CMSQueueUpdateForPayment']);
//Route::get('UpdateForPaymentQueue', [BAEHL7Controller::class, 'UpdateForPaymentQueue']);



//Route::get('GHLErosForHL7', [GHLErosHL7Controller::class, 'ErosForHL7']);
Route::get('GHLCMSQueueUpdateForPayment', [GHLErosHL7Controller::class, 'CMSQueueUpdateForPayment']);

//Route::get('HOMErosForHL7', [HOMErosHL7Controller::class, 'ErosForHL7']);
Route::get('HOMCMSQueueUpdateForPayment', [HOMErosHL7Controller::class, 'CMSQueueUpdateForPayment']);

//Route::get('IMDErosForHL7', [IMDErosHL7Controller::class, 'ErosForHL7']);
Route::get('IMDCMSQueueUpdateForPayment', [IMDErosHL7Controller::class, 'CMSQueueUpdateForPayment']);

//Route::get('MAKErosForHL7', [MAKErosHL7Controller::class, 'ErosForHL7']);
Route::get('MAKCMSQueueUpdateForPayment', [MAKErosHL7Controller::class, 'CMSQueueUpdateForPayment']);

//Route::get('PCSErosForHL7', [PCSErosHL7Controller::class, 'ErosForHL7']);
Route::get('PCSCMSQueueUpdateForPayment', [PCSErosHL7Controller::class, 'CMSQueueUpdateForPayment']);

//Route::get('TARErosForHL7', [TARErosHL7Controller::class, 'ErosForHL7']);
Route::get('TARCMSQueueUpdateForPayment', [TARErosHL7Controller::class, 'CMSQueueUpdateForPayment']);

//Route::get('AuditRollBack', [BAEErosHL7Controller::class, 'AuditRollBack']);

//CENTRAL 
Route::get('cenGetAccessionMapUpdate', [BAEHL7Controller::class, 'cenGetAccessionMapUpdate']);
Route::get('Acc300OnCENStatusAsc', [BAEHL7Controller::class, 'Acc300OnCENStatusAsc']);
Route::get('Acc301OnCENStatusAsc', [BAEHL7Controller::class, 'Acc301OnCENStatusAsc']);
Route::get('Acc400OnCENStatusAsc', [BAEHL7Controller::class, 'Acc400OnCENStatusAsc']);
Route::get('Acc420OnCENStatusAsc', [BAEHL7Controller::class, 'Acc420OnCENStatusAsc']);
Route::get('createJsonFile4HCpdf', [BAEHL7Controller::class, 'createJsonFile4HCpdf']);
// For New Status
Route::get('Acc380OnCENStatusAsc', [BAEHL7Controller::class, 'Acc380OnCENStatusAsc']);
Route::get('Acc390OnCENStatusAsc', [BAEHL7Controller::class, 'Acc390OnCENStatusAsc']);

//Physician Update
Route::get('UpdateQueuePhysicianApproved', [BAEHL7Controller::class, 'UpdateQueuePhysicianApproved']);

//physician rp hl7 creation 
Route::get('ApprovedRPLeadsHL7', [BAEHL7Controller::class, 'ApprovedRPLeadsHL7']);

