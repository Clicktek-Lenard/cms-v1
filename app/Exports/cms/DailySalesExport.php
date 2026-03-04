<?php

namespace App\Exports\cms;

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


class DailySalesExport implements FromCollection, WithHeadings, WithColumnWidths, WithStyles, WithEvents
{
	use Exportable;
	
	public function __construct(array $param)
	{
		$this->dateFrom = $param['dateFrom'];
		$this->dateTo = $param['dateTo'];
	} 
	
	public function headings(): array
	{
		return ['Queue No.', 'Queue Date', 'Patient Fullname', 'Guarantor', 'Physician', 'Item Code', 'Item Name', 'Item Amount', 'Mode of Payment', 'Pay Amount', 'OR No.'];
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
	// return ['Queue No.', 'Queue Date', 'Patient Fullname', 'Guarantor', 'Physician', 'Item Code', 'Item Name', 'Item Amount', 'Mode of Payment', 'Pay Amount', 'OR No.'];
	public function collection()
	{
		return collect(DB::connection('CMS')->table('Queue')
			->leftjoin('QueueStatus', 'Queue.Status', '=', 'QueueStatus.Id')
			->leftjoin('Eros.Patient', 'Queue.IdPatient', '=', 'Eros.Patient.Id')
			->leftjoin('Transactions', 'Queue.Id', '=', 'Transactions.IdQueue')
			
			->where('Queue.Date', '>=', $this->dateFrom)
			->where('Queue.Date', '<=', $this->dateTo)
			->where('Queue.Status', '>=', 210)
			->where('Queue.Status', '<=', 600)
			->get(array('Queue.Code as QCode', 'Queue.Date', 'Patient.FullName', 'NameCompany', 'Transactions.NameDoctor', 'Transactions.CodeItemPrice', 'Transactions.DescriptionItemPrice', 'Transactions.AmountItemPrice')));
				
		
	}

   
}
