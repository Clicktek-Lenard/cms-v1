<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\eros\ErosExtractionController;


Route::get('ErosGetCompanyTransaction', [ErosExtractionController::class, 'ErosGetCompanyTransaction']);
