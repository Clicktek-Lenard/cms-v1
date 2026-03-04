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


class PatientExport implements FromCollection, WithHeadings, WithColumnWidths, WithStyles, WithEvents
{
	use Exportable;
	
	public function __construct(string $param)
	{
		$this->uploadId = $param;
	} 
	
	public function headings(): array
	{
		return ['Eros Code', 'FullName', 'LastName', 'FirstName', 'MiddleName', 'Gender', 'DOB', 'Address', 'Status', 'UploadID'];
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
		return collect(DB::connection('Eros')->select("SELECT `Code`, `FullName`, `LastName`, `FirstName`, `MiddleName`, `Gender`, `DOB`, `Address`, `Status`, `UploadID` from Patient_temp 
		where (`UploadID` like '".$this->uploadId."%' OR `FileStatus` like '".$this->uploadId."%' ) Order by `Id` ASC "));
		
	}

   
}
