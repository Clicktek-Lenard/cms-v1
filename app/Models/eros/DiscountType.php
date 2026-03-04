<?php

namespace App\Models\eros;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class DiscountType extends Model
{
	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'DiscountType';
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
	
	
	public static function getList($status = array() )
	{
		if(empty($status)) return "Missing Param!";
		return DB::connection('Eros')->table('DiscountType')->whereIn('Status', $status);
	}
	
}
