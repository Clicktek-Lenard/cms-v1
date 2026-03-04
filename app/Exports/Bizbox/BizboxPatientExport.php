<?php

namespace App\Exports\Bizbox;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class BizboxPatientExport implements FromCollection, WithHeadings, WithColumnWidths, WithStyles, WithEvents
{
	use Exportable;
	
	public function __construct(string $param)
	{
		$this->uploadId = $param;
	} 
	
	public function headings(): array
	{
		return ['FullName','GENDER','AGE','XRAY','CBC','URINE','STOOL','ECG','Chem','PapSmear','DrugTest','OptionalTest','Remarks','CLASSIFICATION','ASSESSMENT','ADDRESSES','PASTMEDICALHISTORY'];
	}
	
	public function columnWidths(): array
	{
		return ['A' => 17, 'B' => 30, 'C' => 20, 'D' => 20, 'E' => 20, 'F' => 10, 'G' => 10, 'H' => 50, 'I' => 10];
	}
	
	public function styles(Worksheet $sheet)
	{
		return [
		    // Style the first row as bold text.
		    1    => ['font' => ['bold' => true, 'size' => 11]],

		    // Styling a specific cell by coordinate.
		    //'B2' => ['font' => ['italic' => true]],

		    // Styling an entire column.
		    //'C'  => ['font' => ['size' => 16]],
		];
	}
	
	public function registerEvents(): array
	{
		return [
		    AfterSheet::class    => function(AfterSheet $event) {

			$event->sheet->getDelegate()->getStyle('A1:I1')
					->getAlignment()
					->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

		    },
		];
	}

	public function collection()
	{
		return collect(DB::connection('BizBox')->select(" 
		SELECT fullname AS FullName, w.sex as GENDER, age AS AGE, d.workloads_1 AS XRAY, d.workloads_3 AS CBC, workloads_4 AS URINE, workloads_5 AS STOOL, 
          ISNULL(workloads_2, '') AS ECG, ISNULL(workloads_6, '') AS Chem, ISNULL(workloads_7, '') AS PapSmear, ISNULL(workloads_8, '') AS DrugTest, 
          ISNULL(Vcf, '') AS OptionalTest, ISNULL(functional_10, '') AS Remarks, 
          more1 AS CLASSIFICATION, chamber_valves2 AS ASSESSMENT, p.address as ADDRESSES, d.others20 as PASTMEDICALHISTORY
          FROM patinv a
          INNER JOIN patitem b
          ON a.trackno = b.trackno
          INNER JOIN datacenter c
          ON a.dcno = c.dcno
          INNER JOIN exm_2Decho d
          ON a.trackno = d.trackno
          INNER JOIN addrmstr p
          on p.addrcode = c.homecode
          INNER JOIN patient w on
          c.dcno = w.dcno
          LEFT JOIN (
            SELECT trackno, readername
            FROM radiology
            UNION
            SELECT trackno, readername
            FROM radiology) e
          ON b.trackno = e.trackno
          WHERE CAST(a.rendate  AS DATE) BETWEEN '04/25/2023' AND '04/26/2023'
		"));
		
	}

   
}
