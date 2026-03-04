<?php

namespace App\Http\Controllers\cms\api;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class VPNController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$datas = DB::connection('sqlCMS')->select('SHOW SLAVE STATUS')[0];
		
		$x = $datas->Read_Master_Log_Pos;
		$y = $datas->Exec_Master_Log_Pos;
		$p = ( floatval($x) / floatval($y) ) * 100;
		$slave = array();
			array_push($slave, array(
				'MasterHost' => $datas->Master_Host, 
				'MasterUser' => $datas->Master_User,
				'ReadMasterLogPos' => $datas->Read_Master_Log_Pos,
				'ExecMasterLogPos'	=> $datas->Exec_Master_Log_Pos,
				'MasterLogFile' => $datas->Master_Log_File,
				'SlaveIORunning' => $datas->Slave_IO_Running,
				'SlaveSQLRunning' => $datas->Slave_SQL_Running,
				'LastErrno'	=> $datas->Last_Errno,
				'SecondsBehindMaster' => $datas->Seconds_Behind_Master,
				'Percentage'	=> ($datas->Slave_IO_Running == "No" || $datas->Slave_SQL_Running == "No" ) ? 0 : $p
			));
		return $slave;
    }

  
   
}
