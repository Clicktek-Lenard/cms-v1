<?php
namespace App\Http\Controllers\hclab;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\hclab\HCLABPatientMaster;
use App\Models\hclab\HCLABItemMaster;


class HCLABSyncController extends Controller
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

   
	public function PatientMasterSyncFromSMB()
	{
		HCLABPatientMaster::SMBSync();
	}
	
	public function PatientMasterSyncFromALL2TUAZON()
	{
		HCLABPatientMaster::SyncToTUAZON();
	}
	
	public function PatientMasterSyncFromCEB()
	{
		HCLABPatientMaster::CEBSync();
	}
	
	public function TuazonItemSync()
	{
		HCLABItemMaster::TuazonItemSync();
	}
	
	public function TuazonItemDetailsSync()
	{
		HCLABItemMaster::TuazonItemDetailsSync();
	}
	
	public function ItemMasterCEBSync()
	{
		HCLABItemMaster::ItemMasterCEBSync();
	}
	
	public function ItemMasterSMBSync()
	{
		HCLABItemMaster::ItemMasterSMBSync();
	}
	
	
	
	
	
    
	

}
