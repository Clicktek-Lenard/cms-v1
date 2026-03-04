<?php

namespace App\Models\cms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

class Kiosk extends Model
{
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Kiosk';
    protected $connection = 'Queuing'; // Specify the custom connection name
    protected $primaryKey = 'Id';
	protected $fillable = ['Station', 'Status', 'lastClick', 'CurrentRoom', 'numOfCall']; // Add 'Station' to fillable

	/**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
	/**
     * Fixed display all current date.
     *
     * @var array Used
     */
	
	public static function getQueue($station, $statuses)
	{
		return DB::connection('Queuing')
			->table('Kiosk')
			->where('Kiosk.Date', '=', date('Y-m-d'))
			->where('Kiosk.IdBU', session('userClinicCode'))
			->where('Kiosk.Station', 'LIKE', '%' . $station . '%')
			->whereIn('Kiosk.Status', $statuses)
		// ->where('CMS.Queue.Status', '>=', '210')
			// ->where('CMS.Queue.Status', '<=', '650')
			->leftjoin('Patient', 'Kiosk.IdPatient', '=', 'Patient.Id')
			->leftJoin('CMS.Queue', 'Kiosk.IdQueueCMS', '=', 'Queue.Id')
			->select('Kiosk.*', 'Patient.FullName', 'Queue.Code')
			->get();
	}
	 
	public static function getQueuePaid($stations, $statuses, $onSite = null)
	{
		$myDBId = Controller::getMyDBID(); 
		return DB::connection('CMS')
		->table('Queue')
		->leftJoin('QueueStatus', 'CMS.Queue.Status', '=', 'QueueStatus.Id')
		//->leftjoin('Queuing.Patient', 'CMS.Queue.IdPatient', '=', 'Queuing.Patient.Id')
		->leftJoin('Queuing.Kiosk', 'Queuing.Kiosk.IdQueueCMS', '=', 'CMS.Queue.Id')
		->leftJoin('Eros.Patient', 'CMS.Queue.IdPatient', '=', 'Eros.Patient.Id')
		->where(function ($query) use ($onSite) {
			if( empty($onSite) )
			{
				$query->where('CMS.Queue.Date', '=', date('Y-m-d'));
				$query->where('Queuing.Kiosk.Date', '=', date('Y-m-d'));
				$query->where('Queuing.Kiosk.IdBU', session('userClinicCode'));
			}
			else
			{
				$query->whereIn('Queuing.Kiosk.IdBU', ['UAT','IMD']);
			}
		})
		// ->where('Queuing.Kiosk.Station', 'LIKE', '%' . $station . '%')
		->whereIn('Queuing.Kiosk.Status', $statuses)
		->where('CMS.Queue.Status', '>=', '210')
		->where('CMS.Queue.Status', '<=', '650')
		->where(function ($query) use ($stations) {
			foreach ($stations as $station) {
				$query->orWhere('Queuing.Kiosk.Station', 'LIKE', "%$station%");
			}
		})
		
		// ->select('Queuing.Kiosk.*', 'Queue.QFullName', 'CMS.Queue.Code')
			->get(array('Queuing.Kiosk.*', 'Queue.QFullName as FullName', 'CMS.Queue.Code', 'CMS.Queue.InputBy', 'QueueStatus.Name as QueueStatus', 'Eros.Patient.Code as PatientCode'));
	}
	public static function getQueueVitalSigns($station, $statuses)
	{
	   $myDBId = Controller::getMyDBID(); 
		return DB::connection('CMS')->table('Queue')
		   ->leftJoin('Queuing.Kiosk', 'Queuing.Kiosk.IdQueueCMS', '=', 'CMS.Queue.Id')
		   ->leftJoin('CMS.AccessionNo', 'CMS.Queue.Id', '=', 'CMS.AccessionNo.IdQueue')
		   ->leftJoin('Eros.Patient', 'CMS.Queue.IdPatient', '=', 'Eros.Patient.Id')
           ->where('CMS.Queue.Date', '=', date('Y-m-d'))
		   ->where('Queuing.Kiosk.Date', '=', date('Y-m-d'))
		   ->where('Queuing.Kiosk.IdBU', session('userClinicCode'))
		   ->where('Queuing.Kiosk.Station', 'LIKE', '%' . $station . '%')
		   ->whereIn('Queuing.Kiosk.Status', $statuses)
		   ->where('CMS.AccessionNo.Status', '230')
		   ->where('CMS.AccessionNo.Status', '!=', '900')
		   ->groupBy('CMS.Queue.Id')
		   // ->select('Queuing.Kiosk.*', 'Queue.QFullName', 'CMS.Queue.Code')
		   ->get(array('Queuing.Kiosk.*', 'Queue.QFullName as FullName', 'CMS.Queue.Code', 'CMS.Queue.Id as QId', 'Eros.Patient.Code as PID'));
	}

	public static function getQueueConsultation($station, $statuses, $doctorId)
	{
		 $myDBId = Controller::getMyDBID(); 
		return DB::connection('CMS')->table('Queue')
		   ->leftJoin('Queuing.Kiosk', 'Queuing.Kiosk.IdQueueCMS', '=', 'CMS.Queue.Id')
		   ->leftJoin('CMS.Transactions', 'CMS.Queue.Id', '=', 'CMS.Transactions.IdQueue')
		   ->leftJoin('CMS.VitalSign', 'CMS.Queue.Code', '=', 'CMS.VitalSign.QueueCode')
		   ->leftJoin('CMS.AccessionNo', 'CMS.Queue.Id', '=', 'CMS.AccessionNo.IdQueue')
		   ->leftJoin('Eros.Patient', 'CMS.Queue.IdPatient', '=', 'Eros.Patient.Id')
           ->where('CMS.VitalSign.PcpId', '=' ,$doctorId)
		   ->where('CMS.Queue.Date', '=', date('Y-m-d'))
		   ->where('Queuing.Kiosk.Date', '=', date('Y-m-d'))
			->where('Queuing.Kiosk.IdBU', session('userClinicCode'))
			->where('Queuing.Kiosk.Station', 'LIKE', '%' . $station . '%')
			->whereIn('Queuing.Kiosk.Status', $statuses)
			->where('CMS.AccessionNo.Status', '280')
			->groupBy('CMS.Queue.Id')
		   // ->select('Queuing.Kiosk.*', 'Queue.QFullName', 'CMS.Queue.Code')
			->get(array('Queuing.Kiosk.*', 'Queue.QFullName as FullName', 'CMS.Queue.Code', 'CMS.AccessionNo.ItemCode as AItemCode', 'Eros.Patient.Code as PID'));
	} 
	public static function getQueuePaidPerUser($station, $statuses)
	{
		return DB::connection('CMS')
		->table('Queue')
		->leftjoin('Queuing.Patient', 'CMS.Queue.IdPatient', '=', 'Queuing.Patient.Id')
		->leftJoin('Queuing.Kiosk', 'Queuing.Kiosk.IdQueueCMS', '=', 'CMS.Queue.Id')
		->where('CMS.Queue.Date', '=', date('Y-m-d'))
		->where('Queuing.Kiosk.Date', '=', date('Y-m-d'))
			->where('Queuing.Kiosk.IdBU', session('userClinicCode'))
			->where('Queuing.Kiosk.Station', 'LIKE', '%' . $station . '%')
			->whereIn('Queuing.Kiosk.Status', $statuses)
			->where('CMS.Queue.Status', '>=', '210')
			->where('CMS.Queue.Status', '<=', '650')
		
			->select('Queuing.Kiosk.*', 'Queue.QFullName', 'CMS.Queue.Code')
			->get(array('Queuing.Kiosk.*', 'Queue.QFullName', 'CMS.Queue.Code'));
	}

	public static function getImagingQueue($stations, $statuses)
	{
		return DB::connection('CMS')
			->table('Queue')
			->leftJoin('Queuing.Kiosk', 'Queuing.Kiosk.IdQueueCMS', '=', 'CMS.Queue.Id')
			->where('CMS.Queue.Date', '=', date('Y-m-d'))
			->where('Queuing.Kiosk.Date', '=', date('Y-m-d'))
			->where('Queuing.Kiosk.IdBU', session('userClinicCode'))
			->whereIn('Queuing.Kiosk.Status', $statuses)
			->where('CMS.Queue.Status', '>=', '210')
			->where('CMS.Queue.Status', '<=', '650')
			->where(function ($query) use ($stations) {
				foreach ($stations as $station) {
					$query->orWhere('Queuing.Kiosk.Station', 'LIKE', "%$station%");
				}
			})
			->get([
				'Queuing.Kiosk.*', 
				'Queue.QFullName as FullName', 
				'CMS.Queue.Code'
			]);
	}

	 public static function todaysQueue()
	{
		return DB::connection('Queuing')
			->table('Kiosk')
			->where('Kiosk.Date','=', date('Y-m-d'))
			->where('Kiosk.IdBU', session('userClinicCode'))
			->whereColumn('Kiosk.IdPatient', 'Queuing.Patient.Id');
	}
	/**
     * Display current date with $id.
     *
     * @var array Used
     */
	public static function todaysQueueID($id = null)
	{
		//\DB::enableQueryLog();
		if( $id == null) return 'Error: Missing ID';
		return (new static)
			   ->todaysQueue()
			   ->where('Kiosk.Id', $id);
		//return \DB::getQueryLog();
		
	}
	
	public static function todaysQueueStatus($status = array())
	{
		//\DB::enableQueryLog();
		if( empty($status)) return 'Error: Missing Status Id';
		return (new static)
			   ->todaysQueue()
			   ->whereIn('Kiosk.Id', $status);
		//return \DB::getQueryLog();
		die('todaysQueueStatus');
	}
	
	public static function todaysQueueIDStatus($id = null, $status = array())
	{
		//\DB::enableQueryLog();
		if( empty($status) || $id == null) return 'Error: Missing Id or Status Id';
		return (new static)
			   ->todaysQueue()
			   ->whereIn('Kiosk.Status', $status)
			   ->where('Kiosk.Id', $id);
		//return \DB::getQueryLog();
		die('todaysQueueStatus');
	}
}
/*
    public static function receptionQueue()
	{
		return DB::connection('Queueing')
			->table('Kiosk')
			->where('Kiosk.Date', '=', date('Y-m-d'))
			->where('Kiosk.IdBU', session('userClinicCode'))
			->leftJoin('Patient', 'Kiosk.IdPatient', '=', 'Patient.Id')
			->select('Kiosk.*', 'Patient.FullName')
			->get(array('Patient.FullName as PName'));
	}


	public static function extractionQueue()
	{
		return DB::connection('Queueing')
			->table('Kiosk')
			->where('Kiosk.Date', '=', date('Y-m-d'))
			->where('Kiosk.IdBU', session('userClinicCode'))
			->where('Kiosk.Station', '=', 'extraction') // Filter by extraction station
			->where('Kiosk.Status', '=', 'waiting') // Filter by patients waiting at the extraction station
			->leftJoin('Patient', 'Kiosk.IdPatient', '=', 'Patient.Id')
			->select('Kiosk.*', 'Patient.FullName')
			->get(array('Patient.FullName as PName'));
	}


    public static function xrayQueue()
	{
		return DB::connection('Queueing')
			->table('Kiosk')
			->where('Kiosk.Date', '=', date('Y-m-d'))
			->where('Kiosk.IdBU', session('userClinicCode'))
			->where('Kiosk.Station', '=', 'xray') // Filter by extraction station
			->where('Kiosk.Status', '=', 'waiting') // Filter by patients waiting at the extraction station
			->leftJoin('Patient', 'Kiosk.IdPatient', '=', 'Patient.Id')
			->select('Kiosk.*', 'Patient.FullName')
			->get(array('Patient.FullName as PName'));
	}

    public static function consultationQueue()
	{
		return DB::connection('Queueing')
			->table('Kiosk')
			->where('Kiosk.Date', '=', date('Y-m-d'))
			->where('Kiosk.IdBU', session('userClinicCode'))
			->where('Kiosk.Station', '=', 'consultation') // Filter by extraction station
			->where('Kiosk.Status', '=', 'in_progress') // Filter by patients waiting at the extraction station
			->leftJoin('Patient', 'Kiosk.IdPatient', '=', 'Patient.Id')
			->select('Kiosk.*', 'Patient.FullName')
			->get(array('Patient.FullName as PName'));
	}

    public static function releasingQueue()
	{
		return DB::connection('Queueing')
			->table('Kiosk')
			->where('Kiosk.Date', '=', date('Y-m-d'))
			->where('Kiosk.IdBU', session('userClinicCode'))
			->where('Kiosk.Station', '=', 'releasing') // Filter by extraction station
			->where('Kiosk.Status', '=', 'completed') // Filter by patients waiting at the extraction station
			->leftJoin('Patient', 'Kiosk.IdPatient', '=', 'Patient.Id')
			->select('Kiosk.*', 'Patient.FullName')
			->get(array('Patient.FullName as PName'));
	}

}
*/