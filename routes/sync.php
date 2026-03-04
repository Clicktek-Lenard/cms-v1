<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\hclab\HCLABSyncController;
use App\Http\Controllers\hclab\HclabController;
use App\Http\Controllers\hclab\HclabBCKController;

use App\Http\Controllers\eros\PhysicianController;
use App\Http\Controllers\eros\ErosController;
use App\Http\Controllers\dbm\SyncController;




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

Route::get('ResultDeleted', 'App\Http\Controllers\cms\ResultsMonitoringController@resultDeleted');



Route::get('PatientMasterSyncFromSMB', [HCLABSyncController::class, 'PatientMasterSyncFromSMB']);
Route::get('PatientMasterSyncFromCEB', [HCLABSyncController::class, 'PatientMasterSyncFromCEB']);




Route::get('PatientMasterSyncFromALL2TUAZON', [HCLABSyncController::class, 'PatientMasterSyncFromALL2TUAZON']);

// Item Master of Tuazon HCLAB
Route::get('TuazonItemSync', [HCLABSyncController::class, 'TuazonItemSync']);
Route::get('TuazonItemDetailsSync', [HCLABSyncController::class, 'TuazonItemDetailsSync']);
Route::get('ItemMasterCEBSync', [HCLABSyncController::class, 'ItemMasterCEBSync']);
Route::get('ItemMasterSMBSync', [HCLABSyncController::class, 'ItemMasterSMBSync']);



// update Physician Fullname format per Doc. F
Route::get('PhysicianReUpdate', [PhysicianController::class, 'PhysicianReUpdate']);


// HCLAB table for Dashboard

//Route::get('HclabORDH', [HCLABController::class, 'HclabORDH']);
//Route::get('HclabORDS', [HCLABController::class, 'HclabORDS']);
//Route::get('HclabORDD', [HclabBCKController::class, 'HclabORDD']);
// Route::get('HclabTestI', [HclabBCKController::class, 'HclabTestI']);

Route::get('syncHclabORDH', [HCLABController::class, 'syncHclabORDH']);
Route::get('syncHclabORDS', [HCLABController::class, 'syncHclabORDS']);
Route::get('syncHclabORDD', [HCLABController::class, 'syncHclabORDD']);


// Eros Daily Transaction
Route::get('syncErosBillHDR', [ErosController::class, 'ErosBillingGetTransDaily']);


//IMD Branch Receiving
Route::get('IMDcmsMakeHL7360', [SyncController::class, 'cmsMakeHL7360']);
Route::get('IMDmakeAccessionNo', [SyncController::class, 'makeAccessionNo']);
















