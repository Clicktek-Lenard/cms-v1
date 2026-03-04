<?php

namespace App\Models\cms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class QueNumber extends Model
{
	/**
	* The table associated with the model.
	*
	* @var string
	*/
	use HasFactory;
	protected $connection = 'Queuing';
	protected $table = 'QueNumber';
	protected $primaryKey = 'Id';

	protected $fillable = [
		// 'Code',
		'IdBU',
		'Number',
		'Date'
	];
	/**
	* Indicates if the model should be timestamped.
	*
	* @var bool
	*/
	protected $guarded = [];

	public $timestamps = false;
	/**
	* Fixed display all current date.
	*
	* @var array Used
	*/

	static public function getUpdatedNumber($IdBU = NULL)
	{
		if($IdBU == NULL)
			return 'Missing Param BU';
		
			
		$queData = QueNumber::where('Date', date('Y-m-d'))->where('IdBU', $IdBU)->first();
		//print_r($queData);
		$queData->Number = $queData->Number + 1;
// dd($queData->Number);
		$queData->save();
		return $queData->Number;
	}

}
