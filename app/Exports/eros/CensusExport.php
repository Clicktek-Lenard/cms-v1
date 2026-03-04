<?php

namespace App\Exports\eros;

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


class CensusExport implements FromCollection, WithHeadings, WithColumnWidths, WithStyles, WithEvents
{
	use Exportable;
	
	public function __construct(string $param)
	{
		$this->uploadId = $param;
	} 
	
	public function headings(): array
	{
		return ['Transaction No.', 'Transaction Date', 'Patient Code', 'Guarantor Code', 'Guarantor Name', 'Physician Code', 'Physician Name'];
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
		return collect(DB::connection('ErosDump')->table('BILLING_TRX_HDR')
		->where('BTH_TRXDT', '<=', '2023-07-05%')
		->where('BTH_TRXDT', '>=', '2022-07-05%')
		//->where('COMPANY_DETAILS.CD_NAME', 'NOT LIKE', '%DEFAULT%')
		//->leftJoin('COMPANY_DETAILS','BILLING_TRX_HDR.BTH_COMPANY','=','COMPANY_DETAILS.CD_CODE')
		->leftJoin('CLINICIAN_DETAILS','BILLING_TRX_HDR.BTH_CLINICIAN','=','CLINICIAN_DETAILS.CD_CODE')
		->groupBy('CLINICIAN_DETAILS.CD_CODE')

		->get(array('CLINICIAN_DETAILS.*')));
		
	}

   
}
