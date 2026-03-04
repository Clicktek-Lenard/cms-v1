<?php

namespace App\Models\eros;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class BankNames extends Model
{
	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'BankNames';
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
	public static function getNames()
	{
		return DB::connection('Eros')->table('BankNames')
				->where('Status',1)
				->get(array('*'));
	}
	
}
