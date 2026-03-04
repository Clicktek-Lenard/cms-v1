<?php

namespace App\Http\Controllers\ping;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Http\Controllers\Controller;
use App\Models\eros\ErosDB;

use Acamposm\Ping\Ping;
use Acamposm\Ping\PingCommandBuilder;

class PingController extends Controller
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

   
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    //{
//	 return 'ricky';//view('eros.physicianListCreate');
  //  }

	public function index()
	{
		// Create an instance of PingCommand
		$command = (new PingCommandBuilder('192.168.12.1'))->interval(1);

		// Pass the PingCommand instance to Ping and run...
		$ping = (new Ping($command))->run();
		echo "MEDISCAN <br>";
		dd($ping);
		
		// Create an instance of PingCommand
		//$command1 = (new PingCommandBuilder('200.1.0.1'))->interval(1);

		// Pass the PingCommand instance to Ping and run...
		//$ping1 = (new Ping($command1))->run();
		//echo "MALABON <br>";
		//dd($ping1);
		
	}
    
}
