<?php

namespace App\Models\eros;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class AgentEmpName extends Model
{
	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'HRIS';
	protected $primaryKey = 'Id';
	/**
	* Indicates if the model should be timestamped.
	*
	* @var bool
	*/
	public $timestamps = false;
	/**
	* Relationship Table ItemCategory.
	*
	* @var array
	*/
	public static function AgentName()
	{
		return DB::connection('HRIS')->table('EmployeeList')
				->where('Status', 'Active')
				->get(array('EmployeeID', 'EmployeeName'));
	}
	
}