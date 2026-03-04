<?php

namespace App\Http\Controllers\cms;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\eros\ErosDB;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DateTime;


use App\Exports\cms\DailySalesExport;

 require_once dirname(__FILE__).'/../../../../vendor/Spreadsheet/Excel/Writer.php'; 

class TurnaroundTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
	//echo session('userDepartmentCode'); die();
	$userRole = json_decode(session('userRole'));
	$matchedRoles = [];
		foreach ($userRole as $role) {
			$user = trim($role->ldap_role);
			if (strpos($user, '-BRANCH') !== false) {
				$matchedRoles[] = $role->ldap_role;
			}
		}
		$transformedRoles = array_map(function($role) {
			if (preg_match('/\[(.*?)\-BRANCH\]/', $role, $matches)) {
				return $matches[1];
			}
			return $role; 
		}, $matchedRoles);
		if (!empty($transformedRoles)) {
			$clinicCode = $transformedRoles;
		}
	$clinic = ErosDB::getClinicData(NULL,NULL,$clinicCode);
	$clinicName = ErosDB::getClinicData(session('userClinicCode'));
		return view('cms.tatReports',['Clinics' => $clinic, 'clinicName' => $clinicName, 'ClinicCode' => $clinicCode]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $branch = $request->input('Clinic');
        
        function cleanUtf8($value) {
            return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }


        if($request->input('_repType')  == "perPatient")
        {
            $dateFrom = $request->input('dateFrom');
            $dateTo = $request->input('dateTo');
    
            $ymd = date("FjYgia");
        
            $workbook = new \Spreadsheet_Excel_Writer();
            $workbook->send('TURNAROUND-TIME-PER-PATIENT-'.$ymd.'.xls');
            $workbook->setVersion(8);
            $worksheet = $workbook->addWorksheet('ALL');
            $worksheet->setInputEncoding('UTF-8');

            $number_format = $workbook->addFormat();
            $number_format->setAlign('center');
            $number_format->setVAlign('vcenter');
            $number_format->setNumFormat('0000');

            
            $centeredFormat = $workbook->addFormat();
            $centeredFormat->setAlign('center');
            $centeredFormat->setVAlign('vcenter');
            $centeredFormat->setColor('green');  
            
            $format_top_center = $workbook->addFormat();
            $format_top_center->setAlign('top');
            $format_top_center->setAlign('center');
            $format_top_center->setVAlign('vjustify');
            $format_top_center->setVAlign('vcenter');
            $format_top_center->setBold (1);
            $format_top_center->setTextWrap(1);

            $color1 = 40; // #DAE9F8
            $color2 = 41; // #A6C9EC
            $color3 = 42; // #FBE2D5
            $color4 = 43; // #F7C7AC
            $green  = 44; //rgb(255, 77, 77)
            $red    = 45; //rgb(52, 224, 66)
            
            $workbook->setCustomColor($color1, 218, 233, 248);
            $workbook->setCustomColor($color2, 166, 201, 236);
            $workbook->setCustomColor($color3, 251, 226, 213);
            $workbook->setCustomColor($color4, 247, 199, 172);
            $workbook->setCustomColor($green, 52, 224, 66);
            $workbook->setCustomColor($red, 255, 77, 77);

            $tatFormat = $workbook->addFormat();
            $tatFormat->setAlign('center');
            $tatFormat->setVAlign('vcenter');

            $greenFormat = $workbook->addFormat();
            $greenFormat->setAlign('center');
            $greenFormat->setVAlign('vcenter');
            $greenFormat->setFgColor($green);
            
            $redFormat = $workbook->addFormat();
            $redFormat->setAlign('center');
            $redFormat->setVAlign('vcenter');
            $redFormat->setFgColor($red);

            // Colored formats
            $format_fill_1 = $workbook->addFormat();
            $format_fill_1->setAlign('center');
            $format_fill_1->setVAlign('vcenter');
            $format_fill_1->setBold(1);
            $format_fill_1->setTextWrap(1);
            $format_fill_1->setFgColor($color1);
            $format_fill_1->setPattern(1);

            $format_fill_2 = $workbook->addFormat();
            $format_fill_2->setAlign('center');
            $format_fill_2->setVAlign('vcenter');
            $format_fill_2->setBold(1);
            $format_fill_2->setTextWrap(1);
            $format_fill_2->setFgColor($color2);
            $format_fill_2->setPattern(1);

            $format_fill_3 = $workbook->addFormat();
            $format_fill_3->setAlign('center');
            $format_fill_3->setVAlign('vcenter');
            $format_fill_3->setBold(1);
            $format_fill_3->setTextWrap(1);
            $format_fill_3->setFgColor($color3);
            $format_fill_3->setPattern(1);

            $format_fill_4 = $workbook->addFormat();
            $format_fill_4->setAlign('center');
            $format_fill_4->setVAlign('vcenter');
            $format_fill_4->setBold(1);
            $format_fill_4->setTextWrap(1);
            $format_fill_4->setFgColor($color4);
            $format_fill_4->setPattern(1);

    
            $worksheet->setColumn(0,0,11); // QUEUE DATE   
            $worksheet->setColumn(1,1,17); // QUEUE CODE
            $worksheet->setColumn(2,2,40); // PATIENT NAME
            $worksheet->setColumn(3,3,11); // CALL NUMBER  
            $worksheet->setColumn(4,4,17); // QUEUE NUMBER DATE TIME GENERATED FROM KIOSK 
            $worksheet->setColumn(5,5,17); // PATIENT SERVED BY RECEPTIONIST (IN)   
            $worksheet->setColumn(6,6,15); // TURNAROUND TIME (KIOSK TO RECEPTION)  
            $worksheet->setColumn(7,7,17); // TRANSACTION FINISHED   
            $worksheet->setColumn(8,8,15); // SERVED BY
            $worksheet->setColumn(9,9,15); // TAT (RECEPTION TO PAYMENT COMPLETION)
            $worksheet->setColumn(10,10,40);
            $worksheet->setColumn(11,11,13);
            $worksheet->setColumn(12,12,14);

    
            $x = 4;
            $header = array('QUEUE DATE', 'QUEUE CODE', 'PATIENT NAME', 'CALL NUMBER','QUEUE NUMBER DATE TIME GENERATED FROM KIOSK', 'PATIENT SERVED BY RECEPTIONIST (IN)', 'TAT (KIOSK TO RECEPTION)', 'TRANSACTION FINISHED', 'SERVED BY', 'TAT (RECEPTION TO PAYMENT COMPLETION)', 'GUARANTOR', 'TYPE', 'TRANSACTION TYPE');
            for ($col = 0; $col < count($header); $col++) {
                if ($col == 4 || $col == 5) {
                    $worksheet->write($x, $col, $header[$col], $format_fill_1);
                } elseif ($col == 6) {
                    $worksheet->write($x, $col, $header[$col], $format_fill_2);
                } elseif ($col == 7) {
                    $worksheet->write($x, $col, $header[$col], $format_fill_3);
                } elseif ($col == 9) {
                    $worksheet->write($x, $col, $header[$col], $format_fill_4);
                } else {
                    $worksheet->write($x, $col, $header[$col], $format_top_center);
                }
            }
            $x++;

            $result = DB::select(
                'WITH RECURSIVE original_queue AS (
                    SELECT 
                        Id,
                        Id AS OriginalQueueId,
                        AnteDateQueueID
                    FROM CMS.Queue
                    WHERE AnteDateQueueID = 0
                    
                    UNION ALL
                    
                    SELECT 
                        q.Id,
                        o.OriginalQueueId,
                        q.AnteDateQueueID
                    FROM CMS.Queue q
                    JOIN original_queue o ON q.AnteDateQueueID = o.Id
                )

                SELECT 
                    q.Id,
                    q.Code,
                    q.Date,
                    q.QFullName,
                    q.DateTime AS QueueCreateTime,
                    q.InputBy,
                    oq.OriginalQueueId,
                    MIN(CASE WHEN l.Action = "GENERATE CODE" THEN l.DateTime END) AS CodeGenerated,
                    MAX(CASE WHEN l.Action = "IN" THEN l.DateTime END) AS PatientIn,
                    SEC_TO_TIME(
                        TIMESTAMPDIFF(SECOND, 
                            MIN(CASE WHEN l.Action = "GENERATE CODE" THEN l.DateTime END), 
                            MAX(CASE WHEN l.Action = "IN" THEN l.DateTime END)
                        )
                    ) AS TurnaroundMinutes,
                    MIN(CASE WHEN l.Action = "QUEUE CREATE" THEN l.QueueNo END) AS QueueNo,
                    MIN(ph.InputDate) AS PaymentInputDate,
                    SEC_TO_TIME(
                        TIMESTAMPDIFF(SECOND, 
                            q.DateTime, 
                            MIN(ph.InputDate)
                        )
                    ) AS RecepToPayment,
                    SEC_TO_TIME(
                        TIMESTAMPDIFF(SECOND,
                            MAX(CASE WHEN l.Action = "IN" THEN l.DateTime END),
                            MIN(ph.InputDate)
                        )
                    ) AS TotalTurnaroundTime

                FROM CMS.Queue q
                JOIN original_queue oq ON q.Id = oq.Id
                LEFT JOIN Queuing.Logs l ON q.Code = l.CMSQueueCode
                LEFT JOIN CMS.PaymentHistory ph ON oq.OriginalQueueId = ph.IdQueue

                WHERE q.IdBu      = ?
                AND q.Date      >= ?
                AND q.Date      <= ?
                AND q.Status    >= 210
                AND q.Status    <= 600

                GROUP BY q.Id, q.Code, q.Date, q.QFullName, q.InputBy, q.DateTime, oq.OriginalQueueId',
                [$branch, $dateFrom, $dateTo]
            );

            function writeMainRow($worksheet, $x, $row, $number_format, $centeredFormat, $format) {
                $worksheet->write($x, 0, $row->Date);
                $worksheet->write($x, 1, $row->Code, $number_format);
                $worksheet->write($x, 2, $row->QFullName);

                $queueNo = str_pad($row->QueueNo, 4, '0', STR_PAD_LEFT);
                if ($queueNo !== '0000') {
                    $worksheet->writeString($x, 3, $queueNo, $number_format);
                }

                $worksheet->write($x, 4, $row->CodeGenerated);
                $worksheet->write($x, 5, $row->PatientIn);
                $worksheet->write($x, 6, $row->TurnaroundMinutes, $centeredFormat);
                $worksheet->write($x, 7, $row->PaymentInputDate);
                $worksheet->write($x, 8, $row->InputBy);
                $worksheet->write($x, 9, $row->TotalTurnaroundTime, $format);
            }

            foreach ($result as $row) {
                $guarantorInfo = DB::connection('CMS')
                    ->table('Transactions')
                    ->where('IdQueue', $row->OriginalQueueId)
                    ->whereNotNull('IdCompany')
                    ->where('IdCompany', '!=', 0)
                    ->select('IdCompany', 'TransactionType')
                    ->distinct()
                    ->get();

                // Determine the format once per row
                if ($row->TotalTurnaroundTime !== null && $row->TotalTurnaroundTime !== '') {
                    if ($row->TotalTurnaroundTime <= "00:10:00") {
                        $format = $greenFormat;
                    } elseif ($row->TotalTurnaroundTime >= "00:30:00") {
                        $format = $redFormat;
                    } else {
                        $format = $tatFormat;
                    }
                } else {
                    $format = $tatFormat;
                }

                if ($guarantorInfo->isEmpty()) {
                    // No guarantor, write the main row once
                    writeMainRow($worksheet, $x, $row, $number_format, $centeredFormat, $format);
                    $x++;
                } else {
                    // For each guarantor, write the row with company info
                    foreach ($guarantorInfo as $info) {
                        $company = DB::connection('Eros')
                            ->table('Company')
                            ->where('Id', $info->IdCompany)
                            ->select('Name', 'BillingType')
                            ->first();

                        if ($company) {
                            writeMainRow($worksheet, $x, $row, $number_format, $centeredFormat, $format);

                            $worksheet->write($x, 10, $company->Name);
                            $billingType = ($company->BillingType === 'HMO') ? 'HMO/Insurance' : $company->BillingType;
                            $worksheet->write($x, 11, $billingType);
                            $worksheet->write($x, 12, $info->TransactionType);

                            $x++;
                        }
                    }
                }
            }

            $worksheet->writeRow(1, 0, array('Date From', date('M j, Y', strtotime($dateFrom))));
            $worksheet->writeRow(2, 0, array('Date To', date('M j, Y', strtotime($dateTo))));
            $worksheet->writeRow(0, 0, array('Branch', strtoupper($branch)));

            $merged_format = $workbook->addFormat();
            $merged_format->setAlign('center');
            $merged_format->setVAlign('vcenter');
            $merged_format->setBold(1);
            $worksheet->write(2, 4, 'KIOSK', $merged_format);
            $worksheet->mergeCells(2, 4, 3, 6);
            
            // $worksheet->write(2, 7, 'QUEUE', $merged_format);
            // $worksheet->mergeCells(2, 7, 3, 9); // H3 to J4
        
    
            $workbook->close();
        }
        else if($request->input('_repType') == "summary")
        {
            $dateInput = $request->input('dateFrom');
            $date = Carbon::parse($dateInput);

            $year = $request->input('year');
            $month = $request->input('month');

            $monthStart = date("$year-$month-01");
            $monthEnd = date("Y-m-t", strtotime($monthStart));

            $monday = $date->copy()->startOfWeek(Carbon::MONDAY);
            $sunday = $date->copy()->endOfWeek(Carbon::SUNDAY);

            $ymd = date("FjYgia");

            $workbook = new \Spreadsheet_Excel_Writer();
            $workbook->send('TURNAROUND-TIME-SUMMARY-'.$ymd.'.xls');
            $workbook->setVersion(8);
            $worksheet = $workbook->addWorksheet('SUMMARY');
            $worksheet->setInputEncoding('UTF-8');

            $header_format = $workbook->addFormat();
            $header_format->setAlign('center');
            $header_format->setVAlign('vcenter');
            $header_format->setBold(1);
            $header_format->setTextWrap(1);

            $row_format = $workbook->addFormat();
            $row_format->setAlign('vcenter');
            $row_format->setBold(1);
            $row_format->setTextWrap(1);

            $centeredFormat = $workbook->addFormat();
            $centeredFormat->setAlign('center');
            $centeredFormat->setVAlign('vcenter');
            $centeredFormat->setColor('green');

            $bold_format = $workbook->addFormat();
            $bold_format->setBold(1);

            $worksheet->setColumn(0, 0, 11.50);
            $worksheet->setColumn(1, 3, 15);

            $worksheet->setColumn(5, 5, 16.50); // F column
            $worksheet->setColumn(6, 8, 15);    // G to I columns

            $worksheet->setRow(4, 20);
            $worksheet->setRow(5, 20);
            $worksheet->setRow(6, 30);
            $worksheet->setRow(7, 30);
            $worksheet->setRow(8, 30);

            $worksheet->setRow(10, 30);
            $worksheet->setRow(11, 30);
            $worksheet->setRow(12, 30);

            $worksheet->write(0, 0, 'Branch:');
            $worksheet->write(0, 1, strtoupper($branch), $bold_format);
            $worksheet->write(1, 0, 'Date:');
            $worksheet->write(1, 1, Carbon::parse($dateInput)->format('Y-m-d'), $bold_format);
            $worksheet->write(2, 0, 'Week:');
            $worksheet->write(2, 1, $monday->format('Y-m-d'), $bold_format);
            $worksheet->write(2, 2, $sunday->format('Y-m-d'), $bold_format);
            $worksheet->write(3, 0, 'Month:');
            $worksheet->write(3, 1, DateTime::createFromFormat('!m', $month)->format('F'), $bold_format);
            $worksheet->write(3, 2, $year, $bold_format);

            // Header row
            $worksheet->write(5, 0, '', $header_format);
            $worksheet->write(5, 1, 'TAT', $header_format);
            $worksheet->write(5, 2, 'Longest TAT', $header_format);
            $worksheet->write(5, 3, 'Shortest TAT', $header_format);

            $worksheet->write(5, 6, 'TAT', $header_format);
            $worksheet->write(5, 7, 'Longest TAT', $header_format);
            $worksheet->write(5, 8, 'Shortest TAT', $header_format);

            $daily = DB::select(
                'WITH RECURSIVE original_queue AS (
                    SELECT Id, Id AS OriginalQueueId, AnteDateQueueID
                    FROM CMS.Queue
                    WHERE AnteDateQueueID = 0

                    UNION ALL

                    SELECT q.Id, oq.OriginalQueueId, q.AnteDateQueueID
                    FROM CMS.Queue q
                    JOIN original_queue oq ON q.AnteDateQueueID = oq.Id
                )

                SELECT 
                    SEC_TO_TIME(AVG(TIMESTAMPDIFF(SECOND, PatientIn, PaymentInputDate))) AS AvgTotalTurnaroundTime,
                    SEC_TO_TIME(MIN(TIMESTAMPDIFF(SECOND, PatientIn, PaymentInputDate))) AS ShortestTotalTurnaroundTime,
                    SEC_TO_TIME(MAX(TIMESTAMPDIFF(SECOND, PatientIn, PaymentInputDate))) AS LongestTotalTurnaroundTime
                FROM (
                    SELECT 
                        q.Id,
                        MAX(CASE WHEN l.Action = "IN" THEN l.DateTime END) AS PatientIn,
                        MIN(ph.InputDate) AS PaymentInputDate
                    FROM CMS.Queue q
                    JOIN original_queue oq ON q.Id = oq.Id
                    LEFT JOIN Queuing.Logs l ON q.Code = l.CMSQueueCode
                    LEFT JOIN CMS.PaymentHistory ph ON ph.IdQueue = oq.OriginalQueueId
                    WHERE q.IdBU   = ?
                    AND q.Date   = ?
                    AND q.Status    >= 210
                    AND q.Status    <= 600
                    GROUP BY q.Id
                    HAVING PatientIn IS NOT NULL AND PaymentInputDate IS NOT NULL
                ) AS t',
                [$branch, $dateInput]
            );

            $weekly = DB::select(
                'WITH RECURSIVE original_queue AS (
                    SELECT Id, Id AS OriginalQueueId, AnteDateQueueID
                    FROM CMS.Queue
                    WHERE AnteDateQueueID = 0

                    UNION ALL

                    SELECT q.Id, oq.OriginalQueueId, q.AnteDateQueueID
                    FROM CMS.Queue q
                    JOIN original_queue oq ON q.AnteDateQueueID = oq.Id
                )

                SELECT 
                    SEC_TO_TIME(AVG(TIMESTAMPDIFF(SECOND, PatientIn, PaymentInputDate))) AS AvgTotalTurnaroundTime,
                    SEC_TO_TIME(MIN(TIMESTAMPDIFF(SECOND, PatientIn, PaymentInputDate))) AS ShortestTotalTurnaroundTime,
                    SEC_TO_TIME(MAX(TIMESTAMPDIFF(SECOND, PatientIn, PaymentInputDate))) AS LongestTotalTurnaroundTime
                FROM (
                    SELECT 
                        q.Id,
                        MAX(CASE WHEN l.Action = "IN" THEN l.DateTime END) AS PatientIn,
                        MIN(ph.InputDate) AS PaymentInputDate
                    FROM CMS.Queue q
                    JOIN original_queue oq ON q.Id = oq.Id
                    LEFT JOIN Queuing.Logs l ON q.Code = l.CMSQueueCode
                    LEFT JOIN CMS.PaymentHistory ph ON ph.IdQueue = oq.OriginalQueueId
                    WHERE q.IdBU   = ?
                    AND q.Date      >= ?
                    AND q.Date      <= ?
                    AND q.Status    >= 210
                    AND q.Status    <= 600
                    GROUP BY q.Id
                    HAVING PatientIn IS NOT NULL AND PaymentInputDate IS NOT NULL
                ) AS t',
                [$branch, $monday->format('Y-m-d'), $sunday->format('Y-m-d')]
            );

            $monthly = DB::select(
                'WITH RECURSIVE original_queue AS (
                    SELECT Id, Id AS OriginalQueueId, AnteDateQueueID
                    FROM CMS.Queue
                    WHERE AnteDateQueueID = 0

                    UNION ALL

                    SELECT q.Id, oq.OriginalQueueId, q.AnteDateQueueID
                    FROM CMS.Queue q
                    JOIN original_queue oq ON q.AnteDateQueueID = oq.Id
                )

                SELECT 
                    SEC_TO_TIME(AVG(TurnaroundSeconds)) AS AvgTotalTurnaroundTime,
                    SEC_TO_TIME(MIN(TurnaroundSeconds)) AS ShortestTotalTurnaroundTime,
                    SEC_TO_TIME(MAX(TurnaroundSeconds)) AS LongestTotalTurnaroundTime,
                    (
                        SELECT q2.Code
                        FROM (
                            SELECT 
                                q.Id,
                                q.Code,
                                TIMESTAMPDIFF(SECOND, 
                                    MAX(CASE WHEN l.Action = "IN" THEN l.DateTime END),
                                    MIN(ph.InputDate)
                                ) AS TurnaroundSeconds
                            FROM CMS.Queue q
                            JOIN original_queue oq ON q.Id = oq.Id
                            LEFT JOIN Queuing.Logs l ON q.Code = l.CMSQueueCode
                            LEFT JOIN CMS.PaymentHistory ph ON ph.IdQueue = oq.OriginalQueueId
                            WHERE q.IdBU = ?
                            AND q.Date BETWEEN ? AND ?
                            AND q.Status BETWEEN 210 AND 600
                            GROUP BY q.Id, q.Code
                            HAVING TurnaroundSeconds IS NOT NULL
                        ) AS q2
                        ORDER BY q2.TurnaroundSeconds DESC
                        LIMIT 1
                    ) AS LongestTurnaroundCode
                FROM (
                    SELECT 
                        q.Id,
                        q.Code,
                        TIMESTAMPDIFF(SECOND, 
                            MAX(CASE WHEN l.Action = "IN" THEN l.DateTime END),
                            MIN(ph.InputDate)
                        ) AS TurnaroundSeconds
                    FROM CMS.Queue q
                    JOIN original_queue oq ON q.Id = oq.Id
                    LEFT JOIN Queuing.Logs l ON q.Code = l.CMSQueueCode
                    LEFT JOIN CMS.PaymentHistory ph ON ph.IdQueue = oq.OriginalQueueId
                    WHERE q.IdBU = ?
                    AND q.Date      >= ?
                    AND q.Date      <= ?
                    AND q.Status    >= 210
                    AND q.Status    <= 600
                    GROUP BY q.Id, q.Code
                    HAVING TurnaroundSeconds IS NOT NULL
                ) AS t',
                [$branch, $monthStart, $monthEnd, $branch, $monthStart, $monthEnd]
            );

            $monthlyByType = DB::select(
                'WITH RECURSIVE original_queue AS (
                    SELECT Id, Id AS OriginalQueueId, AnteDateQueueID
                    FROM CMS.Queue
                    WHERE AnteDateQueueID = 0

                    UNION ALL

                    SELECT q.Id, oq.OriginalQueueId, q.AnteDateQueueID
                    FROM CMS.Queue q
                    JOIN original_queue oq ON q.AnteDateQueueID = oq.Id
                )

                SELECT 
                    COALESCE(c.BillingType, "CASH") AS BillingType,
                    SEC_TO_TIME(AVG(TurnaroundSeconds)) AS AvgTotalTurnaroundTime,
                    SEC_TO_TIME(MIN(TurnaroundSeconds)) AS ShortestTotalTurnaroundTime,
                    SEC_TO_TIME(MAX(TurnaroundSeconds)) AS LongestTotalTurnaroundTime
                FROM (
                    SELECT 
                        q.Id,
                        q.Code,
                        TIMESTAMPDIFF(SECOND, 
                            MAX(CASE WHEN l.Action = "IN" THEN l.DateTime END),
                            MIN(ph.InputDate)
                        ) AS TurnaroundSeconds,
                        t.IdCompany,
                        t.TransactionType,
                        oq.OriginalQueueId
                    FROM CMS.Queue q
                    JOIN original_queue oq ON q.Id = oq.Id
                    LEFT JOIN Queuing.Logs l ON q.Code = l.CMSQueueCode
                    LEFT JOIN CMS.PaymentHistory ph ON ph.IdQueue = oq.OriginalQueueId
                    LEFT JOIN CMS.Transactions t ON t.IdQueue = oq.OriginalQueueId AND t.IdCompany IS NOT NULL AND t.IdCompany != 0
                    WHERE q.IdBU = ?
                    AND q.Date BETWEEN ? AND ?
                    AND q.Status BETWEEN 210 AND 600
                    GROUP BY q.Id, q.Code, t.IdCompany, t.TransactionType, oq.OriginalQueueId
                    HAVING TurnaroundSeconds IS NOT NULL
                ) AS t
                LEFT JOIN Eros.Company c ON t.IdCompany = c.Id
                GROUP BY BillingType',
                [$branch, $monthStart, $monthEnd]
            );

            $dailyKiosk = DB::select(
                'WITH RECURSIVE original_queue AS (
                    SELECT Id, Id AS OriginalQueueId, AnteDateQueueID
                    FROM CMS.Queue
                    WHERE AnteDateQueueID = 0

                    UNION ALL

                    SELECT q.Id, oq.OriginalQueueId, q.AnteDateQueueID
                    FROM CMS.Queue q
                    JOIN original_queue oq ON q.AnteDateQueueID = oq.Id
                )

                SELECT 
                    SEC_TO_TIME(AVG(TIMESTAMPDIFF(SECOND, CodeGenerated, PatientIn))) AS AvgKioskToReceptionTime,
                    SEC_TO_TIME(MIN(TIMESTAMPDIFF(SECOND, CodeGenerated, PatientIn))) AS ShortestKioskToReceptionTime,
                    SEC_TO_TIME(MAX(TIMESTAMPDIFF(SECOND, CodeGenerated, PatientIn))) AS LongestKioskToReceptionTime
                FROM (
                    SELECT 
                        q.Id,
                        MIN(CASE WHEN l.Action = "GENERATE CODE" THEN l.DateTime END) AS CodeGenerated,
                        MAX(CASE WHEN l.Action = "IN" THEN l.DateTime END) AS PatientIn
                    FROM CMS.Queue q
                    JOIN original_queue oq ON q.Id = oq.Id
                    LEFT JOIN Queuing.Logs l ON q.Code = l.CMSQueueCode
                    WHERE q.IdBU = ?
                    AND q.Date = ?
                    AND q.Status BETWEEN 210 AND 600
                    GROUP BY q.Id
                    HAVING CodeGenerated IS NOT NULL AND PatientIn IS NOT NULL
                ) AS t',
                [$branch, $dateInput]
            );

            $weeklyKiosk = DB::select(
                'WITH RECURSIVE original_queue AS (
                    SELECT Id, Id AS OriginalQueueId, AnteDateQueueID
                    FROM CMS.Queue
                    WHERE AnteDateQueueID = 0

                    UNION ALL

                    SELECT q.Id, oq.OriginalQueueId, q.AnteDateQueueID
                    FROM CMS.Queue q
                    JOIN original_queue oq ON q.AnteDateQueueID = oq.Id
                )

                SELECT 
                    SEC_TO_TIME(AVG(TIMESTAMPDIFF(SECOND, CodeGenerated, PatientIn))) AS AvgKioskToReceptionTime,
                    SEC_TO_TIME(MIN(TIMESTAMPDIFF(SECOND, CodeGenerated, PatientIn))) AS ShortestKioskToReceptionTime,
                    SEC_TO_TIME(MAX(TIMESTAMPDIFF(SECOND, CodeGenerated, PatientIn))) AS LongestKioskToReceptionTime
                FROM (
                    SELECT 
                        q.Id,
                        MIN(CASE WHEN l.Action = "GENERATE CODE" THEN l.DateTime END) AS CodeGenerated,
                        MAX(CASE WHEN l.Action = "IN" THEN l.DateTime END) AS PatientIn
                    FROM CMS.Queue q
                    JOIN original_queue oq ON q.Id = oq.Id
                    LEFT JOIN Queuing.Logs l ON q.Code = l.CMSQueueCode
                    WHERE q.IdBU = ?
                    AND q.Date BETWEEN ? AND ?
                    AND q.Status BETWEEN 210 AND 600
                    GROUP BY q.Id
                    HAVING CodeGenerated IS NOT NULL AND PatientIn IS NOT NULL
                ) AS t',
                [$branch, $monday->format('Y-m-d'), $sunday->format('Y-m-d')]
            );

            $monthlyKiosk = DB::select(
                'WITH RECURSIVE original_queue AS (
                    SELECT Id, Id AS OriginalQueueId, AnteDateQueueID
                    FROM CMS.Queue
                    WHERE AnteDateQueueID = 0

                    UNION ALL

                    SELECT q.Id, oq.OriginalQueueId, q.AnteDateQueueID
                    FROM CMS.Queue q
                    JOIN original_queue oq ON q.AnteDateQueueID = oq.Id
                )

                SELECT 
                    SEC_TO_TIME(AVG(TurnaroundSeconds)) AS AvgKioskToReceptionTime,
                    SEC_TO_TIME(MIN(TurnaroundSeconds)) AS ShortestKioskToReceptionTime,
                    SEC_TO_TIME(MAX(TurnaroundSeconds)) AS LongestKioskToReceptionTime,

                    (
                        SELECT q2.Code
                        FROM (
                            SELECT 
                                q.Id,
                                q.Code,
                                TIMESTAMPDIFF(SECOND, 
                                    MIN(CASE WHEN l.Action = "GENERATE CODE" THEN l.DateTime END),
                                    MAX(CASE WHEN l.Action = "IN" THEN l.DateTime END)
                                ) AS TurnaroundSeconds
                            FROM CMS.Queue q
                            JOIN original_queue oq ON q.Id = oq.Id
                            LEFT JOIN Queuing.Logs l ON q.Code = l.CMSQueueCode
                            WHERE q.IdBU = ?
                            AND q.Date BETWEEN ? AND ?
                            AND q.Status BETWEEN 210 AND 600
                            GROUP BY q.Id, q.Code
                            HAVING TurnaroundSeconds IS NOT NULL
                        ) AS q2
                        ORDER BY q2.TurnaroundSeconds DESC
                        LIMIT 1
                    ) AS LongestTurnaroundCode

                FROM (
                    SELECT 
                        q.Id,
                        q.Code,
                        TIMESTAMPDIFF(SECOND, 
                            MIN(CASE WHEN l.Action = "GENERATE CODE" THEN l.DateTime END),
                            MAX(CASE WHEN l.Action = "IN" THEN l.DateTime END)
                        ) AS TurnaroundSeconds
                    FROM CMS.Queue q
                    JOIN original_queue oq ON q.Id = oq.Id
                    LEFT JOIN Queuing.Logs l ON q.Code = l.CMSQueueCode
                    WHERE q.IdBU = ?
                    AND q.Date BETWEEN ? AND ?
                    AND q.Status BETWEEN 210 AND 600
                    GROUP BY q.Id, q.Code
                    HAVING TurnaroundSeconds IS NOT NULL
                ) AS t',
                [$branch, $monthStart, $monthEnd, $branch, $monthStart, $monthEnd]
            );

            $summary = [
                'Daily' => [
                    'TAT' => $daily[0]->AvgTotalTurnaroundTime ?? '0:00:00',
                    'Longest' => $daily[0]->LongestTotalTurnaroundTime ?? '0:00:00',
                    'Shortest' => $daily[0]->ShortestTotalTurnaroundTime ?? '0:00:00',
                ],
                'Weekly' => [
                    'TAT' => $weekly[0]->AvgTotalTurnaroundTime ?? '0:00:00',
                    'Longest' => $weekly[0]->LongestTotalTurnaroundTime ?? '0:00:00',
                    'Shortest' => $weekly[0]->ShortestTotalTurnaroundTime ?? '0:00:00',
                ],
                'Monthly' => [
                    'TAT' => $monthly[0]->AvgTotalTurnaroundTime ?? '0:00:00',
                    'Longest' => $monthly[0]->LongestTotalTurnaroundTime ?? '0:00:00',
                    // 'Longest' => ($monthly[0]->LongestTotalTurnaroundTime ?? '0:00:00') . 
                    //             ($monthly[0]->LongestTurnaroundCode ? " (Code: {$monthly[0]->LongestTurnaroundCode})" : ''),            
                    'Shortest' => $monthly[0]->ShortestTotalTurnaroundTime ?? '0:00:00',
                ],
            ];

            $summaryKiosk = [
                'Daily' => [
                    'TAT' => $dailyKiosk[0]->AvgKioskToReceptionTime  ?? '0:00:00',
                    'Longest' => $dailyKiosk[0]->LongestKioskToReceptionTime  ?? '0:00:00',
                    'Shortest' => $dailyKiosk[0]->ShortestKioskToReceptionTime  ?? '0:00:00',
                ],
                'Weekly' => [
                    'TAT' => $weeklyKiosk[0]->AvgKioskToReceptionTime  ?? '0:00:00',
                    'Longest' => $weeklyKiosk[0]->LongestKioskToReceptionTime  ?? '0:00:00',
                    'Shortest' => $weeklyKiosk[0]->ShortestKioskToReceptionTime  ?? '0:00:00',
                ],
                'Monthly' => [
                    'TAT' => $monthlyKiosk[0]->AvgKioskToReceptionTime  ?? '0:00:00',
                    'Longest' => $monthlyKiosk[0]->LongestKioskToReceptionTime  ?? '0:00:00',
                    // 'Longest' => ($monthlyKiosk[0]->LongestKioskToReceptionTime ?? '0:00:00') . 
                    //             ($monthlyKiosk[0]->LongestTurnaroundCode ? " (Code: {$monthly[0]->LongestTurnaroundCode})" : ''),            
                    'Shortest' => $monthlyKiosk[0]->ShortestKioskToReceptionTime  ?? '0:00:00',
                ],
            ];

            // Labels for sections
            $periodLabels = [
                'Daily' => 'I. Daily',
                'Weekly' => 'II. Weekly',
                'Monthly' => 'III. Monthly',
            ];

            $kioskPeriodLabels = [
                'Daily' => '1. Daily',
                'Weekly' => '2. Weekly',
                'Monthly' => '3. Monthly',
            ];


            $billingTypeLabels = [
                'Walk-In' => 'A. Walk-In',
                'Corporate' => 'B. Corporate',
                'HMO' => 'C. HMO/Insurance',
            ];

            // Write Daily / Weekly / Monthly section
            $x = 6; // starting row
            foreach ($summary as $period => $data) {
                $worksheet->write($x, 5, $periodLabels[$period] ?? $period, $row_format);
                $worksheet->write($x, 6, explode('.', $data['TAT'])[0], $centeredFormat);
                $worksheet->write($x, 7, explode('.', $data['Longest'])[0], $centeredFormat);
                $worksheet->write($x, 8, explode('.', $data['Shortest'])[0], $centeredFormat);

                // Kiosk to Reception
                $kioskData = $summaryKiosk[$period] ?? ['TAT' => '0:00:00', 'Longest' => '0:00:00', 'Shortest' => '0:00:00'];
                $worksheet->write($x, 0, $kioskPeriodLabels[$period] ?? $period, $row_format);
                $worksheet->write($x, 1, explode('.', $kioskData['TAT'])[0], $centeredFormat);
                $worksheet->write($x, 2, explode('.', $kioskData['Longest'])[0], $centeredFormat);
                $worksheet->write($x, 3, explode('.', $kioskData['Shortest'])[0], $centeredFormat);

                $x++;
            }


            // Write Billing Type section
            $billingTypeRows = [
                'Walk-In' => 10,
                'Corporate' => 11,
                'HMO' => 12,
            ];

            foreach ($monthlyByType as $row) {
                $billingType = $row->BillingType;
                $rowIndex = $billingTypeRows[$billingType] ?? null;

                if ($rowIndex !== null) {
                    $worksheet->write($rowIndex, 5, $billingTypeLabels[$billingType] ?? $billingType, $row_format);
                    $worksheet->write($rowIndex, 6, explode('.', $row->AvgTotalTurnaroundTime)[0], $centeredFormat);
                    $worksheet->write($rowIndex, 7, explode('.', $row->LongestTotalTurnaroundTime)[0], $centeredFormat);
                    $worksheet->write($rowIndex, 8, explode('.', $row->ShortestTotalTurnaroundTime)[0], $centeredFormat);
                }
            }

            $merged_format = $workbook->addFormat();
            $merged_format->setAlign('center');
            $merged_format->setVAlign('vcenter');
            $merged_format->setBold(1);
            $worksheet->write(4, 0, 'KIOSK TO RECEPTION', $merged_format);
            $worksheet->mergeCells(4, 0, 4, 3);

            $worksheet->write(4, 5, 'RECEPTION TO PAYMENT COMPLETION', $merged_format);
            $worksheet->mergeCells(4, 5, 4, 8);


            $workbook->close();
        }
        else if($request->input('_repType') == "releasing")
        {

            $dateFrom = $request->input('dateFrom');
            $dateTo = $request->input('dateTo');

            $ymd = date("FjYgia");
		
            $workbook = new \Spreadsheet_Excel_Writer();
            $workbook->send('TURNAROUND-TIME-RELEASING-'.$ymd.'.xls');
            $workbook->setVersion(8);
            $worksheet = $workbook->addWorksheet('ALL');
            $worksheet->setInputEncoding('UTF-8');

            $number_format = $workbook->addFormat();
            $number_format->setAlign('center');
            $number_format->setVAlign('vcenter');
            $number_format->setNumFormat('0');

            $centeredFormat = $workbook->addFormat();
            $centeredFormat->setAlign('center');
            $centeredFormat->setVAlign('vcenter');
            $centeredFormat->setColor('green');
            
            $format_top_center = $workbook->addFormat();
            $format_top_center->setAlign('top');
            $format_top_center->setAlign('center');
            $format_top_center->setVAlign('vjustify');
            $format_top_center->setVAlign('vcenter');
            $format_top_center->setBold (1);
            $format_top_center->setTextWrap(1);
            
            $worksheet->setColumn(0,0,9); // QUEUE DATE   
            $worksheet->setColumn(1,1,11); //CALL NUMBER
            $worksheet->setColumn(2,2,17); //CODE GENERATED
            $worksheet->setColumn(3,3, 17); // CALL
            $worksheet->setColumn(4,4,17);  // RESULT RELEASE
            $worksheet->setColumn(5,5,15);  // PATIENT WAITING TIME
		
            $x = 4;
			
			$header = array('DATE', 'CALL NUMBER', 'CODE GENERATED', 'CALL', 'RESULT RELEASE', 'PATIENT WAITING TIME');
			$worksheet->writeRow($x++,0,$header, $format_top_center);

            // $releasing = DB::select("
            //     SELECT 
            //         DATE(DateTime) AS Date,
            //         KioskId,
            //         QueueNo,
            //         MAX(CASE WHEN Action = 'GENERATE CODE' THEN DateTime END) AS GenerateCodeTime,
            //         MAX(CASE WHEN Action = 'CALL' THEN DateTime END) AS CallTime,
            //         MAX(CASE WHEN Action = 'IN' THEN DateTime END) AS InTime,
            //         SEC_TO_TIME(
            //             TIMESTAMPDIFF(
            //                 SECOND,
            //                 MAX(CASE WHEN Action = 'GENERATE CODE' THEN DateTime END),
            //                 MAX(CASE WHEN Action = 'IN' THEN DateTime END)
            //             )
            //         ) AS TurnaroundTime
            //     FROM Queuing.Logs
            //     WHERE Room = 'Releasing'
            //         AND Action IN ('GENERATE CODE', 'CALL', 'IN')
            //         AND ActionBy = ?
            //         AND DateTime >= ? 
            //         AND DateTime <= ?
            //     GROUP BY DATE(DateTime), KioskId, QueueNo
            // ", [$branch, $dateFrom, $dateTo]);

            $releasing = DB::select("
                SELECT 
                    DATE(gc.DateTime) AS Date,
                    gc.QueueNo,
                    gc.KioskId,
                    MAX(gc.DateTime) AS GenerateCodeTime,
                    MAX(CASE WHEN l.Action = 'CALL' THEN l.DateTime END) AS CallTime,
                    MAX(CASE WHEN l.Action = 'IN' THEN l.DateTime END) AS InTime,
                    SEC_TO_TIME(
                        TIMESTAMPDIFF(
                            SECOND,
                            MAX(gc.DateTime),
                            MAX(CASE WHEN l.Action = 'IN' THEN l.DateTime END)
                        )
                    ) AS TurnaroundTime
                FROM Queuing.Logs gc
                LEFT JOIN Queuing.Logs l
                    ON l.KioskId = gc.KioskId
                    AND l.Action IN ('CALL', 'IN')
                    AND l.Room = 'Releasing'
                    AND DATE(l.DateTime) BETWEEN ? AND ?
                WHERE gc.Action = 'GENERATE CODE'
                    AND gc.ActionBy = ?
                    AND gc.Room = 'Releasing'
                    AND DATE(gc.DateTime) BETWEEN ? AND ?
                GROUP BY DATE(gc.DateTime), gc.QueueNo, gc.KioskId
            ", [$dateFrom, $dateTo, $branch, $dateFrom, $dateTo]);


            foreach ($releasing as $row) {
                $worksheet->write($x, 0, $row->Date);
                $queueNo = str_pad($row->QueueNo, 4, '0', STR_PAD_LEFT);
                if ($queueNo !== '0000') {
                    $worksheet->writeString($x, 1, $queueNo, $number_format); // Write only if not '0000'
                }
                $worksheet->write($x, 2, $row->GenerateCodeTime);
                $worksheet->write($x, 3, $row->CallTime);
                $worksheet->write($x, 4, $row->InTime);
                $worksheet->write($x, 5, $row->TurnaroundTime, $centeredFormat);
                $x++;
            }

            $worksheet->writeRow(1, 0, array('Date From', date('M j, Y', strtotime($dateFrom))));
            $worksheet->writeRow(2, 0, array('Date To', date('M j, Y', strtotime($dateTo))));
            $worksheet->writeRow(0, 0, array('Branch', strtoupper($branch)));

            $workbook->close();

        }
        else if($request->input('_repType') == "releasingsummary")
        {
            $dateInput = $request->input('dateFrom');
            $date = Carbon::parse($dateInput);  

            $year = date('Y');
            $month = $request->input('month');

            $monthStart = date("$year-$month-01");
            $monthEnd = date("Y-m-t", strtotime($monthStart));

            $monday = $date->copy()->startOfWeek(Carbon::MONDAY);
            $sunday = $date->copy()->endOfWeek(Carbon::SUNDAY);

            $ymd = date("FjYgia");

            $workbook = new \Spreadsheet_Excel_Writer();
            $workbook->send('TURNAROUND-TIME-RELEASING-SUMMARY-'.$ymd.'.xls');
            $workbook->setVersion(8);
            $worksheet = $workbook->addWorksheet('SUMMARY');
            $worksheet->setInputEncoding('UTF-8');

            $header_format = $workbook->addFormat();
            $header_format->setAlign('center');
            $header_format->setVAlign('vcenter');
            $header_format->setBold(1);
            $header_format->setTextWrap(1);

            $centeredFormat = $workbook->addFormat();
            $centeredFormat->setAlign('center');
            $centeredFormat->setVAlign('vcenter');
            $centeredFormat->setColor('green');

            $bold_format = $workbook->addFormat();
            $bold_format->setBold(1);

            $worksheet->setColumn(0, 0, 9);
            $worksheet->setColumn(1, 3, 15);

            $worksheet->setRow(5, 20);
            $worksheet->setRow(6, 30);
            $worksheet->setRow(7, 30);
            $worksheet->setRow(8, 30);

            $worksheet->write(0, 0, 'Branch:');
            $worksheet->write(0, 1, strtoupper($branch), $bold_format);
            $worksheet->write(1, 0, 'Date:');
            $worksheet->write(1, 1, Carbon::parse($dateInput)->format('Y-m-d'), $bold_format);
            $worksheet->write(2, 0, 'Week:');
            $worksheet->write(2, 1, $monday->format('Y-m-d'), $bold_format);
            $worksheet->write(2, 2, $sunday->format('Y-m-d'), $bold_format);
            $worksheet->write(3, 0, 'Month:');
            $worksheet->write(3, 1, DateTime::createFromFormat('!m', $month)->format('F'), $bold_format);

            // Header row
            $worksheet->write(5, 0, '', $header_format);
            $worksheet->write(5, 1, 'TAT', $header_format);
            $worksheet->write(5, 2, 'Longest TAT', $header_format);
            $worksheet->write(5, 3, 'Shortest TAT', $header_format);

            $daily = DB::select("
                SELECT
                    SEC_TO_TIME(AVG(TurnaroundSeconds)) AS AvgTurnaroundTime,
                    SEC_TO_TIME(MIN(TurnaroundSeconds)) AS ShortestTurnaroundTime,
                    SEC_TO_TIME(MAX(TurnaroundSeconds)) AS LongestTurnaroundTime
                FROM (
                    SELECT 
                        TIMESTAMPDIFF(
                            SECOND,
                            MAX(CASE WHEN Action = 'GENERATE CODE' THEN DateTime END),
                            MAX(CASE WHEN Action = 'IN' THEN DateTime END)
                        ) AS TurnaroundSeconds
                    FROM Queuing.Logs
                    WHERE Room = 'Releasing'
                        AND Action IN ('GENERATE CODE', 'CALL', 'IN')
                        AND DATE(DateTime) = ?
                    GROUP BY QueueNo
                ) AS turnaround_data
            ", [$dateInput]);

            $weekly = DB::select("
                SELECT
                    SEC_TO_TIME(AVG(TurnaroundSeconds)) AS AvgTurnaroundTime,
                    SEC_TO_TIME(MIN(TurnaroundSeconds)) AS ShortestTurnaroundTime,
                    SEC_TO_TIME(MAX(TurnaroundSeconds)) AS LongestTurnaroundTime
                FROM (
                    SELECT 
                        TIMESTAMPDIFF(
                            SECOND,
                            MAX(CASE WHEN Action = 'GENERATE CODE' THEN DateTime END),
                            MAX(CASE WHEN Action = 'IN' THEN DateTime END)
                        ) AS TurnaroundSeconds
                    FROM Queuing.Logs
                    WHERE Room = 'Releasing'
                        AND Action IN ('GENERATE CODE', 'CALL', 'IN')
                        AND DATE(DateTime) >= ?
                        AND DATE(DateTime) <= ?
                    GROUP BY DATE(DateTime), QueueNo
                ) AS turnaround_data
            ", [$monday->format('Y-m-d'), $sunday->format('Y-m-d')]);

            $monthly = DB::select("
                SELECT
                    SEC_TO_TIME(AVG(TurnaroundSeconds)) AS AvgTurnaroundTime,
                    SEC_TO_TIME(MIN(TurnaroundSeconds)) AS ShortestTurnaroundTime,
                    SEC_TO_TIME(MAX(TurnaroundSeconds)) AS LongestTurnaroundTime
                FROM (
                    SELECT 
                        TIMESTAMPDIFF(
                            SECOND,
                            MAX(CASE WHEN Action = 'GENERATE CODE' THEN DateTime END),
                            MAX(CASE WHEN Action = 'IN' THEN DateTime END)
                        ) AS TurnaroundSeconds
                    FROM Queuing.Logs
                    WHERE Room = 'Releasing'
                        AND Action IN ('GENERATE CODE', 'CALL', 'IN')
                        AND DATE(DateTime) BETWEEN ? AND ?
                    GROUP BY DATE(DateTime), QueueNo
                ) AS turnaround_data
            ", [$monthStart, $monthEnd]);


            $summary = [
                'Daily' => [
                    'TAT' => $daily[0]->AvgTurnaroundTime ?? '0:00:00',
                    'Longest' => $daily[0]->LongestTurnaroundTime ?? '0:00:00',
                    'Shortest' => $daily[0]->ShortestTurnaroundTime ?? '0:00:00',
                ],
                'Weekly' => [
                    'TAT' => $weekly[0]->AvgTurnaroundTime ?? '0:00:00',
                    'Longest' => $weekly[0]->LongestTurnaroundTime ?? '0:00:00',
                    'Shortest' => $weekly[0]->ShortestTurnaroundTime ?? '0:00:00',
                ],
                'Monthly' => [
                    'TAT' => $monthly[0]->AvgTurnaroundTime ?? '0:00:00',
                    'Longest' => $monthly[0]->LongestTurnaroundTime ?? '0:00:00',
                    // 'Longest' => ($monthly[0]->LongestTotalTurnaroundTime ?? '0:00:00') . 
                    //             ($monthly[0]->LongestTurnaroundCode ? " (Code: {$monthly[0]->LongestTurnaroundCode})" : ''),            
                    'Shortest' => $monthly[0]->ShortestTurnaroundTime ?? '0:00:00',
                ],
            ];


            $x = 6; // starting row
            foreach ($summary as $period => $data) {
                $worksheet->write($x, 0, $period, $header_format);
                $worksheet->write($x, 1, explode('.', $data['TAT'])[0], $centeredFormat);
                $worksheet->write($x, 2, $data['Longest'], $centeredFormat);
                $worksheet->write($x, 3, $data['Shortest'], $centeredFormat);
                $x++;
            }

            $workbook->close();
        }


// WITH TRACEBACK AMMENDMENT QUERY
        // $result = DB::select(
        //     'SELECT 
        //         q.Id,
        //         q.Code,
        //         q.Date,
        //         q.QFullName,
        //         q.InputBy,
        //         MIN(CASE WHEN l.Action = "GENERATE CODE" THEN l.DateTime END) AS CodeGenerated,
        //         MAX(CASE WHEN l.Action = "IN" THEN l.DateTime END) AS PatientIn,
        //         ROUND(
        //             TIMESTAMPDIFF(SECOND, 
        //                 MIN(CASE WHEN l.Action = "GENERATE CODE" THEN l.DateTime END), 
        //                 MAX(CASE WHEN l.Action = "IN" THEN l.DateTime END)
        //             ) / 60, 2
        //         ) AS TurnaroundMinutes,
        //         MIN(CASE WHEN l.Action = "QUEUE CREATE" THEN l.DateTime END) AS QueueCreateTime,
        //         MIN(ph.InputDate) AS PaymentInputDate,
        //         ROUND(
        //             TIMESTAMPDIFF(SECOND, 
        //                 MIN(CASE WHEN l.Action = "QUEUE CREATE" THEN l.DateTime END), 
        //                 MIN(ph.InputDate)
        //             ) / 60, 2
        //         ) AS RecepToPayment
        //      FROM CMS.Queue q
        //      LEFT JOIN Queuing.Logs l ON q.Code = l.CMSQueueCode
        //      LEFT JOIN CMS.PaymentHistory ph ON q.Id = ph.IdQueue
        //      WHERE q.IdBu = ?
        //        AND q.Date BETWEEN ? AND ?
        //        AND q.Status BETWEEN 300 AND 500
        //      GROUP BY q.Id, q.Code, q.Date, q.QFullName, q.InputBy',
        //     [$branch, $dateFrom, $dateTo]
        // );

        // $result = DB::select(
        //     'WITH RECURSIVE original_queue AS (
        //         SELECT 
        //             Id,
        //             Id AS OriginalQueueId,
        //             AnteDateQueueID
        //         FROM CMS.Queue
        //         WHERE AnteDateQueueID = 0
                
        //         UNION ALL
                
        //         SELECT 
        //             q.Id,
        //             o.OriginalQueueId,
        //             q.AnteDateQueueID
        //         FROM CMS.Queue q
        //         JOIN original_queue o ON q.AnteDateQueueID = o.Id
        //     )

        //     SELECT 
        //         q.Id,
        //         q.Code,
        //         q.Date,
        //         q.QFullName,
        //         q.DateTime AS QueueCreateTime,
        //         q.InputBy,
        //         oq.OriginalQueueId,
        //         MIN(CASE WHEN l.Action = "GENERATE CODE" THEN l.DateTime END) AS CodeGenerated,
        //         MAX(CASE WHEN l.Action = "IN" THEN l.DateTime END) AS PatientIn,
        //         SEC_TO_TIME(
        //             TIMESTAMPDIFF(SECOND, 
        //                 MIN(CASE WHEN l.Action = "GENERATE CODE" THEN l.DateTime END), 
        //                 MAX(CASE WHEN l.Action = "IN" THEN l.DateTime END)
        //             )
        //         ) AS TurnaroundMinutes,
        //         MIN(CASE WHEN l.Action = "QUEUE CREATE" THEN l.QueueNo END) AS QueueNo,
        //         MIN(ph.InputDate) AS PaymentInputDate,
        //         SEC_TO_TIME(
        //             TIMESTAMPDIFF(SECOND, 
        //                 q.DateTime, 
        //                 MIN(ph.InputDate)
        //             )
        //         ) AS RecepToPayment,
        //         SEC_TO_TIME(
        //             TIMESTAMPDIFF(SECOND,
        //                 MIN(CASE WHEN l.Action = "GENERATE CODE" THEN l.DateTime END),
        //                 MIN(ph.InputDate)
        //             )
        //         ) AS TotalTurnaroundTime

        //     FROM CMS.Queue q
        //     JOIN original_queue oq ON q.Id = oq.Id
        //     LEFT JOIN Queuing.Logs l ON q.Code = l.CMSQueueCode
        //     LEFT JOIN CMS.PaymentHistory ph ON oq.OriginalQueueId = ph.IdQueue

        //     WHERE q.IdBu      = ?
        //     AND q.Date      >= ?
        //     AND q.Date      <= ?
        //     AND q.Status    >= 210
        //     AND q.Status    <= 600

        //     GROUP BY q.Id, q.Code, q.Date, q.QFullName, q.InputBy, q.DateTime, oq.OriginalQueueId',
        //     [$branch, $dateFrom, $dateTo]
        // );
        
        // dd($result);

            // $monthlyByType = DB::select(
            //     'WITH RECURSIVE original_queue AS (
            //         SELECT Id, Id AS OriginalQueueId, AnteDateQueueID
            //         FROM CMS.Queue
            //         WHERE AnteDateQueueID = 0

            //         UNION ALL

            //         SELECT q.Id, oq.OriginalQueueId, q.AnteDateQueueID
            //         FROM CMS.Queue q
            //         JOIN original_queue oq ON q.AnteDateQueueID = oq.Id
            //     )

            //     SELECT 
            //         CASE 
            //             WHEN c.BillingType = "HMO" THEN "HMO/Insurance"
            //             WHEN c.BillingType IS NULL THEN "Walk-In"
            //             ELSE c.BillingType
            //         END AS BillingType,

            //         SEC_TO_TIME(AVG(TurnaroundSeconds)) AS AvgTotalTurnaroundTime,
            //         SEC_TO_TIME(MIN(TurnaroundSeconds)) AS ShortestTotalTurnaroundTime,
            //         SEC_TO_TIME(MAX(TurnaroundSeconds)) AS LongestTotalTurnaroundTime

            //     FROM (
            //         SELECT 
            //             q.Id,
            //             q.Code,
            //             TIMESTAMPDIFF(SECOND, 
            //                 MAX(CASE WHEN l.Action = "IN" THEN l.DateTime END),
            //                 MIN(ph.InputDate)
            //             ) AS TurnaroundSeconds,
            //             oq.OriginalQueueId
            //         FROM CMS.Queue q
            //         JOIN original_queue oq ON q.Id = oq.Id
            //         LEFT JOIN Queuing.Logs l ON q.Code = l.CMSQueueCode
            //         LEFT JOIN CMS.PaymentHistory ph ON ph.IdQueue = oq.OriginalQueueId
            //         WHERE q.IdBU = ?
            //         AND q.Date BETWEEN ? AND ?
            //         AND q.Status BETWEEN 210 AND 600
            //         GROUP BY q.Id, q.Code, oq.OriginalQueueId
            //         HAVING TurnaroundSeconds IS NOT NULL
            //     ) AS t
            //     LEFT JOIN CMS.Transactions tr ON tr.IdQueue = t.OriginalQueueId
            //     LEFT JOIN Eros.Company c ON tr.IdCompany = c.Id

            //     GROUP BY BillingType',
            //     [$branch, $monthStart, $monthEnd]
            // );
	}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
