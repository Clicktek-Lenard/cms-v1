<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\hl7\DTUErosHL7Controller;
use App\Http\Controllers\hl7\GHLErosHL7Controller;
use App\Http\Controllers\hl7\HOMErosHL7Controller;
use App\Http\Controllers\hl7\IMDErosHL7Controller;

use App\Http\Controllers\hl7\MAKErosHL7Controller;

use App\Http\Controllers\hl7\PCSErosHL7Controller;
use App\Http\Controllers\hl7\TARErosHL7Controller;

use App\Http\Controllers\hl7\DTUHL7Controller;
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



Route::get('DTUHL7', [DTUHL7Controller::class, 'HL7']);
Route::get('DTUcmsMakeHL7File', [DTUHL7Controller::class, 'cmsMakeHL7File']);
Route::get('DTUupdateCMSHL7QueueStatus', [DTUHL7Controller::class, 'updateCMSHL7QueueStatus']);
Route::get('DTUUpdateForPaymentQueue', [DTUHL7Controller::class, 'UpdateForPaymentQueue']);


Route::get('IMDHL7', [IMDHL7Controller::class, 'HL7']);
Route::get('IMDcmsMakeHL7File', [IMDHL7Controller::class, 'cmsMakeHL7File']);
Route::get('IMDupdateCMSHL7QueueStatus', [IMDHL7Controller::class, 'updateCMSHL7QueueStatus']);
Route::get('IMDUpdateForPaymentQueue', [IMDHL7Controller::class, 'UpdateForPaymentQueue']);


Route::get('HOMHL7', [HOMHL7Controller::class, 'HL7']);
Route::get('HOMcmsMakeHL7File', [HOMHL7Controller::class, 'cmsMakeHL7File']);
Route::get('HOMupdateCMSHL7QueueStatus', [HOMHL7Controller::class, 'updateCMSHL7QueueStatus']);
Route::get('HOMUpdateForPaymentQueue', [HOMHL7Controller::class, 'UpdateForPaymentQueue']);



Route::get('HCLABUpdateEmail', [DTUErosHL7Controller::class, 'HCLABUpdateEmail']);



//Route::get('DTUErosForHL7', [DTUErosHL7Controller::class, 'ErosForHL7']);
Route::get('DTUCMSQueueUpdateForPayment', [DTUErosHL7Controller::class, 'CMSQueueUpdateForPayment']);
//Route::get('UpdateForPaymentQueue', [DTUHL7Controller::class, 'UpdateForPaymentQueue']);



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

//Route::get('AuditRollBack', [DTUErosHL7Controller::class, 'AuditRollBack']);

//CENTRAL 
Route::get('cenGetAccessionMapUpdate', [DTUHL7Controller::class, 'cenGetAccessionMapUpdate']);
Route::get('Acc300OnCENStatusAsc', [DTUHL7Controller::class, 'Acc300OnCENStatusAsc']);
Route::get('Acc301OnCENStatusAsc', [DTUHL7Controller::class, 'Acc301OnCENStatusAsc']);
Route::get('Acc400OnCENStatusAsc', [DTUHL7Controller::class, 'Acc400OnCENStatusAsc']);
Route::get('Acc420OnCENStatusAsc', [DTUHL7Controller::class, 'Acc420OnCENStatusAsc']);


