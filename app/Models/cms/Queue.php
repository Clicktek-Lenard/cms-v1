<?php

namespace App\Models\cms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


use App\Http\Controllers\Controller;


class Queue extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Queue';
    protected $primaryKey = 'Id';
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
	public static function todaysQueue()
	{
		if( session('userClinicCode') == 'ICT')
		{
			return DB::connection('CMS')->table('Queue')
			->where('Queue.Date', date('Y-m-d'))
			->where('Queue.IdBU', session('userClinicCode'))
			->leftJoin('Eros.Patient', 'Queue.IdPatient', '=', 'Eros.Patient.Id')
			->leftJoin('QueueStatus', 'Queue.Status', '=', 'QueueStatus.Id');
		}
		else
		{
			return DB::connection('CMS')->table('Queue')
			->where('Queue.Date', date('Y-m-d'))
			->where('Queue.IdBU', session('userClinicCode'))
			->leftJoin('Eros.Patient', 'Queue.IdPatient', '=', 'Eros.Patient.Id')
			->leftJoin('QueueStatus', 'Queue.Status', '=', 'QueueStatus.Id');
		}
	}
	public static function BMtodaysQueue()
	{
			return DB::connection('CMS')->table('Queue')
			->where('Queue.Date', date('Y-m-d'))
			->where('Queue.IdBU', session('userClinicCode'))
			->whereIn('Queue.Status', [203, 212])
			->leftJoin('Eros.Patient', 'Queue.IdPatient', '=', 'Eros.Patient.Id')
			->leftJoin('QueueStatus', 'Queue.Status', '=', 'QueueStatus.Id');
		//}
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
			   ->where('Queue.Id', $id);
		//return \DB::getQueryLog();
		
	}
	
	public static function todaysQueueStatus($status = array())
	{
		//\DB::enableQueryLog();
		if( empty($status)) return 'Error: Missing Status Id';
		return (new static)
			   ->todaysQueue()
			   ->whereIn('Queue.Status', $status);
		//return \DB::getQueryLog();
		die('todaysQueueStatus');
	}
	
	public static function todaysQueueIDStatus($id = null, $status = array())
	{
		//\DB::enableQueryLog();
		if( empty($status) || $id == null) return 'Error: Missing Id or Status Id';
		return (new static)
			   ->todaysQueue()
			   ->whereIn('Queue.Status', $status)
			   ->where('Queue.Id', $id);
		//return \DB::getQueryLog();
		die('todaysQueueStatus');
	}
	
	/**
     * Display Past Queue default yesterday.
     *
     * @var array to be Used
     */
	public static function pastQueue($pName = null )
	{
		/*
		if( $date == null)
			$date = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d')))); 

		return Queue::where('Date', $date)
               ->orderBy('Status', 'asc');
	       */
	      // $date = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d')))); 
	       
	       return DB::connection('CMS')->table('Queue')
			->where(function($q) use ($pName ) {
					$q->where('Queue.Date', '<', date('Y-m-d') );
					$q->where('Queue.IdBU', session('userClinicCode') );
					if( !empty($pName))
					{
						$q->whereRaw("MATCH(FullName) AGAINST(?)", array($pName) );	
					}
			})
			->take(1000)
			->leftJoin('Eros.Patient', 'Queue.IdPatient', '=', 'Eros.Patient.Id')
			->leftJoin('QueueStatus', 'Queue.Status', '=', 'QueueStatus.Id')
			->orderBy('Queue.Date', 'DESC');

	}
	
	public static function pastQueueID($id = null)
	{
		//\DB::enableQueryLog();
		if( $id == null) return 'Error: Missing ID';
		return (new static)
			   ->pastQueue()
			   ->where('Queue.Id', $id);
		//return \DB::getQueryLog();
		
	}
	
	public static function pastQueueIDStatus($id = null, $status = array())
	{
		//\DB::enableQueryLog();
		if( empty($status) || $id == null) return 'Error: Missing Id or Status Id';
		return (new static)
			   ->pastQueue()
			   ->whereIn('Queue.Status', $status)
			   ->where('Queue.Id', $id);
		//return \DB::getQueryLog();
		die('todaysQueueStatus');
	}
	
	/**
     * Insert current date.
     *
     * @var array Used
     */
	
	/**
     * Update current date.
     *
     * @var array Used
     */
	public static function postUpdate($request,$id)
	{
		$dataTime = date('Y-m-d H:i:s');
		
		
		//update queue
		return Queue::where('Id',$id)
				->lockForUpdate()
				->update([
					'IdCompany'	=> $request->input('company')
					,'AccessionNo'	=> $request->input('accession') 
					,'Status'		=> 200
					,'UpdateDate'	=> date('Y-m-d')
					,'AgePatient'	=>  $request->input('age')
					,'UpdateBy'	=> Auth::user()->username
					
				]); 
	}
	public function insertCheckQueue($datas = array())
	{
		$iData = DB::connection('mysql')->select("SELECT `Id` FROM Queue WHERE `IdCompany` = '".$datas['CompanyId']."' and `IdPatient` = '".$datas['PatientId']."'  and `Status` = 100  ");
		if(count($iData) == 0)
		{
			$dataTime = date('Y-m-d H:i:s');
			$max = DB::connection('mysql')->select("SELECT SUBSTR(MAX(Code),-4) as iMax from Queue where Code like '".date('Ymd')."%' " );
			$xMax = ($max[0]->iMax != 0)?$max[0]->iMax+1:1;
		
			return $queueId = Queue::insertGetId([
				'IdBU'		=> session('userBU'),
				'IdCompany'	=> $datas['CompanyId'],
				'Code'		=> date('Ymd').sprintf('%04d', $xMax),
				'Date' 		=> date('Y-m-d'),
				'DateTime' 	=> $dataTime,
				'IdPatient'		=> $datas['PatientId'],
				'AgePatient'	=> 0,
				'AccessionNo'	=> 0,
				'Status'		=> 100,
				'InputBy'		=> Auth::user()->username
			]);
		
		}
		
		return $iData[0]->Id;
	
	}
	
	
	/**
     * Relationship Table Patients.
     *
     * @var array to be confirm
     */
	public function patient()
    {
        return $this->hasone('App\Models\cms\Patient', 'Id', 'IdPatient');
    }

	public static function todaysQueueDate($date = null)
	{
		//\DB::enableQueryLog();
		if( $date == null) return 'Error: Missing ID';
		return (new static)
			   ->todaysQueue()
			   ->where('Queue.Status', 200)
			   ->where('Queue.UpdateDate', $date);
		//return \DB::getQueryLog();
		
	}
	
	public static function sendOutQueue()
	{
		return DB::connection('CMS')
		->table('Receiving')
		->leftJoin('Queue', 'Receiving.IdQueue', '=', 'Queue.Id')
		->where('Receiving.IdBuFrom', '!=', session('userClinicCode'))
		->where('Receiving.IdBuTo', '=', session('userClinicCode'))
		->where('Receiving.Status', '=', 'Send Out')
		->whereNull('Receiving.SendOutStatus')
		->where('Queue.Status', '>=', 300)
        ->where('Queue.Status', '<=', 500)
		->groupBy('Receiving.IdQueue');
	
	}

	public static function sendOutQueueId($id)
	{
		return DB::connection('CMS')->table('Queue')
		->leftJoin('Eros.Patient', 'Queue.IdPatient', '=', 'Eros.Patient.Id')
		->leftJoin('QueueStatus', 'Queue.Status', '=', 'QueueStatus.Id')
		->where('Queue.Id', $id);
	
	}

	public static function toSendOutQueue()
	{
		return DB::connection('CMS')
			->table('Receiving')
			->leftJoin('Queue', 'Receiving.IdQueue', '=', 'Queue.Id')
			->leftJoin('AccessionNo', function ($join) {
				$join->on('Receiving.IdTransaction', '=', 'AccessionNo.IdTransaction')
					 ->on('Receiving.ItemCode', '=', 'AccessionNo.ItemCode');
			})
			->where('Receiving.IdBuFrom', '=', session('userClinicCode'))
			->where('Receiving.Status', '=', 'For Send Out')
			->whereNull('Receiving.SendOutStatus')
			->where('Queue.Status', '>=', 300)
			->where('Queue.Status', '<=', 500)
			->select(
				'Receiving.*',
				'Queue.*',
				'AccessionNo.*',
				'Receiving.Id as ReceivingId'
			);
	}

	public static function specimenReceiving()
	{
		return DB::connection('CMS')
			->table('Receiving')
			->leftJoin('Queue', 'Receiving.IdQueue', '=', 'Queue.Id')
			->leftJoin('AccessionNo', function ($join) {
				$join->on('Receiving.IdTransaction', '=', 'AccessionNo.IdTransaction')
					 ->on('Receiving.ItemCode', '=', 'AccessionNo.ItemCode');
			})
			// ->where('Receiving.IdBuFrom', '=', session('userClinicCode'))
			->where('Receiving.Status', '=', 'Specimen Received')
			->whereNull('Receiving.SendOutStatus')
			->where('Queue.Status', '>=', 300)
			->where('Queue.Status', '<=', 620)
			->where('AccessionNo.Status', '>=', 280)
			->where('AccessionNo.Status', '<=', 500)
			->select(
				'Receiving.*',
				'Queue.*',
				'AccessionNo.ItemSubGroup',
				'AccessionNo.QueueCode',
				'AccessionNo.ItemCode',
				'AccessionNo.ItemDescription',
				'Receiving.Id as ReceivingId'
			);
	}

	public static function laboratoryNonBloodReceiving($batchCode)
	{
		return DB::connection('CMS')
			->table('Receiving')
			->leftJoin('Queue', 'Receiving.IdQueue', '=', 'Queue.Id')
			->leftJoin('AccessionNo', function ($join) {
				$join->on('Receiving.IdTransaction', '=', 'AccessionNo.IdTransaction')
					 ->on('Receiving.ItemCode', '=', 'AccessionNo.ItemCode');
			})
			// ->where('Receiving.IdBuFrom', '=', session('userClinicCode'))
			->where('Receiving.SendoutStatus', '=', 'Send Out')
			->whereNotNull('Receiving.BatchCode')
			->where('Receiving.BatchCode', '=', $batchCode)
			->where('Queue.Status', '>=', 300)
			->where('Queue.Status', '<=', 500)
			->select(
				'Receiving.*',
				'Queue.*',
				'AccessionNo.*',
				'Receiving.Id as ReceivingId'
			);
	}

	public static function laboratoryBloodReceiving($batchCode)
	{
		return DB::connection('CMS')->table('Receiving')
			->leftJoin('Queue', 'Receiving.IdQueue', '=', 'Queue.Id')
			->leftJoin('AccessionNo', function ($join) {
				$join->on('Receiving.IdTransaction', '=', 'AccessionNo.IdTransaction')
					->on('Receiving.ItemCode', '=', 'AccessionNo.ItemCode');
			})
			// ->where('Receiving.IdBuFrom', '=', session('userClinicCode'))
			->where('Receiving.Status', '!=', 'Rejected')
			->whereNotNull('Receiving.BloodBatchCode')
			->whereRaw("JSON_EXTRACT(BloodBatchCode, '$.\"{$batchCode}\"') IS NOT NULL")
			->where('Queue.Status', '>=', 300)
			->where('Queue.Status', '<=', 500)
			->select(
				'Receiving.*',
				'Queue.*',
				'AccessionNo.*',
				'Receiving.Id as ReceivingId'
			);
	}

	public static function rejectedSpecimen()
	{
		return DB::connection('CMS')
			->table('Receiving')
			->leftJoin('Queue', 'Receiving.IdQueue', '=', 'Queue.Id')
			->leftJoin('AccessionNo', function ($join) {
				$join->on('Receiving.IdTransaction', '=', 'AccessionNo.IdTransaction')
					 ->on('Receiving.ItemCode', '=', 'AccessionNo.ItemCode');
			})
			// ->where('Receiving.IdBuFrom', '=', session('userClinicCode'))
			->where('Receiving.Status', '=', 'Rejected')
			->orWhere('Receiving.SendOutStatus', '=', 'Rejected')
			->where('Queue.Status', '>=', 300)
			->where('Queue.Status', '<=', 500)
			->select(
				'Receiving.*',
				'Queue.*',
				'AccessionNo.*',
				'Receiving.Id as ReceivingId'
			);
	}
}
