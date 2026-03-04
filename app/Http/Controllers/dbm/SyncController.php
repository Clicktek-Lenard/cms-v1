<?php

namespace App\Http\Controllers\dbm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Zip;

use App\Http\Controllers\hl7\IMDHL7Controller;
use App\Http\Controllers\cms\api\hPDFController;

class SyncController extends Controller
{

	public function makeAccessionNo()
	{
		$BUCode = ['IMD'];
		DB::connection('CMS')->beginTransaction();
			$alpha = array('AA');
			$alphaY = array('M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');	

			$ATrans =  DB::connection('CMS')->table('AccessionNo')
									//->leftJoin('Eros.Patient', 'Queue.IdPatient', '=', 'Eros.Patient.Id')
									->where('AccessionNo.Status', '=','311') // 250 
									->whereIn('AccessionNo.IdBU', $BUCode)
									->whereIn('AccessionNo.ReceivedBU', ['BAE'])
									->whereIn('AccessionNo.ItemGroup', ['LABORATORY'])
									//->where('AccessionNo.Date', '=', date('Y-m-d'))
									//->where('Queue.ErosStatus', 'NOT LIKE', 'reUpdate')
									//->where('Queue.Id', '=', '674183')
									->limit(1)
									->orderBy('AccessionNo.Id', 'DESC')
									->groupBy('AccessionNo.IdQueue')
									->get(array('AccessionNo.IdQueue as QId', 'AccessionNo.QueueCode as QCode', 'AccessionNo.IdBU'));
			//dd($ATrans);
			foreach($ATrans as $que)
			{
				$x = 0;
				$y = 0; //IMD UPDATE 1-21-2025 FOR IMAGING
				
				//checking status from 260 upto 650
				$gbDocComLab = DB::connection('CMS')->table('AccessionNo')->whereIn('AccessionNo.ReceivedBU', ['BAE'])->where('IdQueue', $que->QId)->where('Status','311')->where('Type', 'LIKE', 'LABORATORY')->groupBy('IdDoctor', 'IdCompany')->get(array('*'));
				
				foreach($gbDocComLab as $gbDC)
				{
					$status =  DB::connection('CMS')->table('AccessionNo')->whereIn('AccessionNo.ReceivedBU', ['BAE'])->where('IdQueue', $que->QId)->where('Status','>=','260')->where('Status','<=','650')->where('Type', 'LIKE', 'LABORATORY')
						->where('IdCompany', $gbDC->IdCompany)->where('IdDoctor', $gbDC->IdDoctor)
						->groupBy('IdDoctor', 'IdCompany')->get(array('AccessionNo'));
					if( count($status) !=0 && !empty($status[0]->AccessionNo) )
					{
						$alphaNew = $status[0]->AccessionNo;
					}
					else
					{
						$lastCheck = DB::connection('CMS')->table('AccessionNo')->whereIn('AccessionNo.ReceivedBU', ['BAE'])->where('IdQueue', $que->QId)->where('Status','>=','260')->where('Status','<=','650')->where('Type', 'LIKE', 'LABORATORY')
						->groupBy('IdDoctor', 'IdCompany')->get(array('AccessionNo'));
						if( count($lastCheck) != 0 && !empty($lastCheck[0]->AccessionNo))
						{
							$alphaNew = $que->QCode.$alpha[count($lastCheck)];
						}
						else
						{
							$alphaNew = $que->QCode.$alpha[$x];
							$x++;
						}
					}

					DB::connection('CMS')->table('AccessionNo')->whereIn('AccessionNo.ReceivedBU', ['BAE'])->where('IdQueue', $que->QId)->where('Status','311')->where('Type', 'LIKE', 'LABORATORY')
					->where('IdCompany', $gbDC->IdCompany)->where('IdDoctor', $gbDC->IdDoctor)
					->update(['Status' => 360, 'AccessionNo' => $alphaNew]);
				}
				//checking status from 260 upto 650 
				$status = DB::connection('CMS')->table('AccessionNo')->where('IdQueue', $que->QId)->where('Status','>=','260')->where('Status','<=','650')->where('Type', 'LIKE', 'IMAGING')->get(array('*'));
				if( count($status) != 0 )
				{
					$y = count($status);
				}
				$gbDocComImaging = DB::connection('CMS')->table('AccessionNo')->where('IdQueue', $que->QId)->where('Status','311')->where('Type', 'LIKE', 'IMAGING')->groupBy('IdDoctor', 'IdCompany', 'IdTransaction','ItemCode')->get(array('*'));
				
				foreach($gbDocComImaging as $gbDC)
				{
					DB::connection('CMS')->table('AccessionNo')->where('IdQueue', $que->QId)->where('Status','311')->where('Type', 'LIKE', 'IMAGING')->where('IdTransaction', $gbDC->IdTransaction)
					->where('IdCompany', $gbDC->IdCompany)->where('IdDoctor', $gbDC->IdDoctor)->where('ItemCode', 'LIKE', $gbDC->ItemCode)
					->update(['Status' => 360, 'AccessionNo' => $que->QCode.$alphaY[$y]]);
					$y++;
				}
				
				DB::connection('CMS')->table('Queue')->where('Id', $que->QId)->update(['Status' => 360]);
			}
			
			DB::connection('CMS')->commit();  
	}
	
	public function cmsMakeHL7360()
	{	
		$BUCode = ['UAT','IMD'];
		$qTrans =  DB::connection('CMS')->table('AccessionNo')
					//->where('AccessionNo.Date', '=', date('Y-m-d'))
					->where('AccessionNo.Status', '=', 360) //Processing HL7
					->whereIn('AccessionNo.IdBU', $BUCode)
					->whereIn('AccessionNo.ReceivedBU', ['BAE'])
					//->where('AccessionNo.IdQueue', '=', '17625485')
					->limit(1)
					->orderBy('AccessionNo.Id', 'ASC')
					->get(array('AccessionNo.IdQueue as QId', 'AccessionNo.Date', 'AccessionNo.QueueCode as QCode', 'AccessionNo.IdBU'));

		//dd($qTrans);

		foreach ($qTrans as $que)
		{
			//check if payment made has been paid
			$accCount = DB::connection('CMS')->table('AccessionNo')->where('IdQueue', $que->QId)->get(array('Id'));
			$selects  = DB::connection('CMS')->table('Transactions')->where('IdQueue', '=', $que->QId)->get(array('Status','SystemUpdateTime'));
			$forConsultation = 0;
			$specimen = 0;
			$fullyPaid = 0;
			//print_r($selects);
			//die();
			foreach($selects as $select)
			{
				if( $select->Status == '300' ) //For Specimen
				{
					$specimen = 1;
				}
				else if( $select->Status == '280' ) //For Consultation
				{
					$forConsultation = 1;
				}
				else if( $select->Status == '210' ) //Fully Paid
				{
					$fullyPaid = 1;
				}
			}
			
			//$mins = (strtotime(date('Y-m-d H:i:s')) - strtotime($que->DateTime)) / 60;
			
			if( $specimen == 1  && $forConsultation == 0 )
			{
				DB::connection('CMS')->table('Queue')->where('Id', $que->QId)->update(['Status' => 300]);
			}
			else if( $specimen == 0  && $forConsultation == 1 )
			{
				DB::connection('CMS')->table('Queue')->where('Id', $que->QId)->update(['Status' => 280]);
			}
			else if( $fullyPaid == 1 && count($accCount) == 0 )
			{
				DB::connection('CMS')->table('Queue')->where('Id', $que->QId)->update(['Status' => 500]); // Completed
			}
			
			
			
			//consultation only
			$accDatas = DB::connection('CMS')->table('AccessionNo')->where('IdQueue', $que->QId)->where('Status', 280)->groupBy('AccessionNo')->get(array('*'));
			foreach($accDatas as $acc)
			{
				DB::connection('CMS')->table('Queue')->where('Status', 360)->where('Id', $que->QId)->update(['Status' => 280]);
			}
			
			$accDatas = DB::connection('CMS')->table('AccessionNo')->where('IdQueue', $que->QId)->where('Status', 360)->groupBy('AccessionNo')->get(array('*'));
			//dd($accDatas);
		
			foreach($accDatas as $acc)
			{
				echo "<br>QueueId => ". $que->QId;

				$labGroup 	= "LABORATORY"; 
				//$radGroup 	= "IMAGING"; 
			
				$labResult  = DB::select('CALL CMS.HL7LABv2(?,?,?,?)', [$acc->AccessionNo, $acc->IdQueue, $labGroup, "BAE" ]);
				//$radResult  = DB::select('CALL CMS.HL7RAD(?,?,?)', [$acc->AccessionNo, $acc->IdQueue, $radGroup]);
				//dd($labResult);
				if (!empty($labResult)) {

					$generatedHL7Message = $labResult[0]->HL7LAB;
					
					if (strpos($generatedHL7Message, 'OBR') !== false) {
						
						$generatedHL7Message = str_replace("\n", "\r\n", $generatedHL7Message);
						
						$filePathLab = public_path('HL7_LAB') . '/' .$acc->AccessionNo.'-ORM^O01-LAB.hl7';
						file_put_contents($filePathLab, $generatedHL7Message);
			
						$filePathBackup = public_path('HL7_FILES/BU_HL7LAB') . '/' .$acc->AccessionNo.'-ORM^O01-LAB.hl7';
						file_put_contents($filePathBackup, $generatedHL7Message);

						$logFileDate = date('ymd');
						$logFilePath = public_path('HL7_FILES/HL7_LOGS/' . $logFileDate . '.log');
						$logMessage = "[" . date('Y-m-d H:i:s') . "] AccessionNo: " . $acc->AccessionNo . " - HL7 Message:\n" . $generatedHL7Message . "\n\n";
						file_put_contents($logFilePath, $logMessage, FILE_APPEND);

						echo "<br>Successfully generated! => LABORATORY";
					} 
				}
				
				
				DB::connection('CMS')->table('AccessionNo')->where('Status', 360)->where('IdQueue', $acc->IdQueue)->where('AccessionNo', '=', $acc->AccessionNo )->update(['Status' => 370]);
				
			}
			
			// new status id
			$stillForPayment = DB::connection('CMS')->table('Transactions')->where('IdQueue', '=', $que->QId)->where('Status', '=', '201')->get(array('Id', 'Status'));
			if( count($stillForPayment) != 0 )
			{
				$iStatus = 201;
				DB::connection('CMS')->table('Queue')->where('Id', $que->QId)->update(['Status' => $iStatus]);
			}
			else
			{	
				// used accession status instead 
				$accStatus = SyncController::accessionStatus(['IdBU' => $BUCode, 'Date' => $que->Date,  'QueueCode' => $que->QCode , 'IdQueue' => $que->QId, 'ReceivedBU' => 'BAE']);
				$specimen = 0;
				$completed = 0;
				
				foreach($accStatus as $select)
				{
					if( $select->Status == '300' ) //For Specimen
					{
						$specimen = 1;
					}
					else if( $select->Status == '500' ) //Completed
					{
						$completed = 1;
					}
				}
				
				if( $specimen == 1  )
				{
					DB::connection('CMS')->table('Queue')->where('Id', $que->QId)->update(['Status' => 300]);
				}
				else if( $completed == 1 )
				{
					DB::connection('CMS')->table('Queue')->where('Id', $que->QId)->update(['Status' => 500]); // Completed
				}
				
			}
			
		}
		
		

		return "<br>DONE => ";
	}
	
	static function accessionStatus($param = [])
	{
		if(empty($param))
		{
			return "Missing Param!";
		}
		 
		return $accs = DB::connection('CMS')->table('AccessionNo')
		 ->where(function($q) use ($param) {
			 foreach($param as $key=> $val)
			 {
				if( is_array($val) )
				{
					$q->whereIn($key, $val);
				}
				else
				{
					$q->where($key, $val);
				}
			 }
		    })
		   ->orderBy('IdTransaction', 'ASC')
		    ->get(array('Id', 'Status', 'IdTransaction'));
	}


}