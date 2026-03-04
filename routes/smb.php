<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\smb\SMBController;


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


Route::get('SMBCompanyUpdate', [SMBController::class, 'SMBCompanyUpdate']);
Route::get('SMBItemPriceUpdate', [SMBController::class, 'SMBItemPriceUpdate']);
Route::get('SMBPhysicianUpdate', [SMBController::class, 'SMBPhysicianUpdate']);







//TEMP Reload Option
Route::get('ErosItemMasterReload', [ItemReloadController::class, 'ErosItemMasterReload']);









