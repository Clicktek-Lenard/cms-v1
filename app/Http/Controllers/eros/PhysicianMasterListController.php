<?php

namespace App\Http\Controllers\eros;                                     //pcp v2

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;

use App\Http\Controllers\Controller;
use App\Models\eros\ErosDB;
use App\Models\eros\Queue;

require_once dirname(__FILE__).'/../../../../vendor/Spreadsheet/Excel/Writer.php'; 

class PhysicianMasterListController extends Controller
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
     public function create()
    {
		//return view('eros.physicianListCreate');
    }

    public function index()
    {
   
	$physicianData = ErosDB::getDoctorDatas();

	//return view('eros.physicianList', ['physicianData' => $physicianData]);
	return view('eros.physicianMasterList', ['physicianData' => $physicianData]);
	
    }

    public function store(Request $request)  
	{	
        $ymd = date("FjYgia");
		$workbook = new \Spreadsheet_Excel_Writer();
		$workbook->send('Doctors-Masterlist-' . $ymd . '.xls');
		$workbook->setVersion(8);
		$worksheet = $workbook->addWorksheet('Doctors_MasterList');
		$worksheet->setInputEncoding('UTF-8');

		// === Column Header Format ===
		$format_col_header = $workbook->addFormat();
		$format_col_header->setAlign('center');
		$format_col_header->setVAlign('vcenter');
		$format_col_header->setBold(1);
		$format_col_header->setTextWrap(1);
		$format_col_header->setFgColor('gray');
		$format_col_header->setPattern(1);

		// === Columns width ===
		$worksheet->setColumn(0, 0, 40); // Full Name
		$worksheet->setColumn(1, 1, 30); // PRC No.
		$worksheet->setColumn(2, 2, 50); // Specialization
		$worksheet->setColumn(3, 3, 15); // Branch
		$worksheet->setColumn(4, 4, 40); // Branch Duty
		$worksheet->setColumn(5, 5, 30); // Schedule Day
		$worksheet->setColumn(6, 6, 30); // Schedule Time Start
		$worksheet->setColumn(7, 7, 30); // Schedule Time End
		$worksheet->setColumn(8, 8, 40); // Type

		// === Optional Filters ===
		$branch = $request->input('branch');
		$specialist = $request->input('specialist');

		$query = DB::connection('Eros')->table('Physician')->where('Status', 'Approved')->whereIn('SubGroup', ['PCP', 'SPL']);

		if ($branch) {
			$query->where('BranchCode', $branch);
		}
		if ($specialist) {
			$query->where('Specialist', $specialist);
		}

		$physicians = $query->get();

		// === Column Headers ===
		$headers = ['FULL NAME', 'PRC NO.', 'SPECIALIZATION', 'BRANCH', 'BRANCH DUTY', 'SCHEDULE DAY', 'TIME START', 'TIME END', 'POSITION'];
		$worksheet->writeRow(0, 0, $headers, $format_col_header);

		// === Write Data ===
		$x = 1;
		foreach ($physicians as $p) {
			$nwdBranch = is_string($p->NWDBranch) ? @json_decode($p->NWDBranch, true) : $p->NWDBranch;
			$schedule = is_string($p->Schedule) ? @json_decode($p->Schedule, true) : $p->Schedule;
			$timeStart = is_string($p->TimeStart) ? @json_decode($p->TimeStart, true) : $p->TimeStart;
			$timeEnd = is_string($p->TimeEnd) ? @json_decode($p->TimeEnd, true) : $p->TimeEnd;

			$nwdBranch = is_array($nwdBranch) ? implode(', ', $nwdBranch) : ($nwdBranch ?? '');
			$schedule = is_array($schedule) ? implode(', ', $schedule) : ($schedule ?? '');
			$timeStart = is_array($timeStart) ? implode(', ', $timeStart) : ($timeStart ?? '');
			$timeEnd = is_array($timeEnd) ? implode(', ', $timeEnd) : ($timeEnd ?? '');

			$type = $p->Specialist === 'Yes' ? 'SPECIALIST' : ($p->Specialist === 'No' ? 'PRIMARY CARE PHYSICIAN' : ($p->Specialist ?? ''));

			$worksheet->writeRow($x++, 0, [
				$p->FullName,
				$p->PRCNo,
				$p->Description,
				$p->BranchCode,
				$nwdBranch,
				$schedule,
				$timeStart,
				$timeEnd,
				$type,
			]);
		}

		$workbook->close();
		die();
	}


}
