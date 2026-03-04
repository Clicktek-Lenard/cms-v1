<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\lin\LINController;


//TEMP Reload Option
use App\Http\Controllers\eros\ItemReloadController;


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





//TEMP Reload Option
Route::get('ErosItemMasterReload', [ItemReloadController::class, 'ErosItemMasterReload']);


// HL7 from Eros
Route::get('LINErosForHL7', [LINController::class, 'LINErosForHL7']);
Route::get('CMSQueueUpdateForPayment', [LINController::class, 'CMSQueueUpdateForPayment']);
Route::get('HCLABUpdateEmail', [LINController::class, 'HCLABUpdateEmail']);










