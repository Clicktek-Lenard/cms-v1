<?php

namespace App\Http\Controllers\cms\pages\queue;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QueueResendHL7 extends Controller
{
    public function resendHL7(Request $request)
    {
        $queueId = $request->input('queueId');
        //dd($queueId);
    
        if (!empty($queueId)) {

            DB::connection('CMS')->table('Queue')
            ->where('Id', $queueId)
            ->update([
                'Status'		=> 260
            ]);

            DB::connection('CMS')->table('Transactions')
            ->where('IdQueue', $queueId)
            ->update([
                'Status'		=> 250
            ]);

            DB::connection('CMS')->table('AccessionNo')
            ->where('IdQueue', $queueId)
            ->update([
                'Status'		=> 260
		,'RISFinalized'	=> NULL
            ]);

            return response()->json(['success' => true, 'message' => 'HL7 sent successfully.']);

        } else {
           
            return response()->json(['success' => false, 'message' => 'Failed to send HL7.']);
        }
        
    }

    public function pastresendHL7(Request $request)
    {
        $queueId = $request->input('queueId');

		//dd($queueId);

        $accDatas = DB::connection('CMS')->table('AccessionNo')->where('IdQueue', $queueId)->groupBy('AccessionNo')->get(array('*'));
		
		foreach($accDatas as $acc)
		{
			$labGroup 	= "LABORATORY"; 
			$radGroup 	= "IMAGING"; 
			
			$labResult  = DB::select('CALL CMS.HL7LAB(?,?,?)', [$acc->AccessionNo, $acc->IdQueue, $labGroup]);
			$radResult  = DB::select('CALL CMS.HL7RAD(?,?,?)', [$acc->AccessionNo, $acc->IdQueue, $radGroup]);

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

				} 
			}

			if (!empty($radResult)) {

				$generatedHL7Message = $radResult[0]->HL7RAD;
				$subGroups = array('2DECHO', 'ABPM', 'BMD', 'CTSCAN', 'ECG', 'FIBROSCAN', 'GEN UTZ', 'HOLTER', 'MAMMO', 'OB UTZ', 'PFT', 'TREADMILL', 'VASCULAR UTZ', 'XRAY');

				if (strpos($generatedHL7Message, 'OBR') !== false) {

					$generatedHL7Message = str_replace("\n", "\r\n", $generatedHL7Message);

					foreach ($subGroups as $sGroup) {
						if (strpos($generatedHL7Message, $sGroup) !== false) {
							$filePathRad = public_path("HL7_RAD/{$sGroup}") . '/' . $acc->AccessionNo . '-ORM^O01-RAD.hl7';
							file_put_contents($filePathRad, $generatedHL7Message);
						}
					}

					$filePathBackup = public_path('HL7_FILES/BU_HL7RAD') . '/' . $acc->AccessionNo . '-ORM^O01-RAD.hl7';
					file_put_contents($filePathBackup, $generatedHL7Message);

					$logFileDate = date('ymd');
					$logFilePath = public_path('HL7_FILES/HL7_LOGS/' . $logFileDate . '.log');
					$logMessage = "[" . date('Y-m-d H:i:s') . "] AccessionNo: " . $acc->AccessionNo . " - HL7 Message:\n" . $generatedHL7Message . "\n\n";
					file_put_contents($logFilePath, $logMessage, FILE_APPEND);
					
				}
			}			
		}

        return response()->json(['success' => true, 'message' => 'HL7 sent successfully.']);
    }
    
} 