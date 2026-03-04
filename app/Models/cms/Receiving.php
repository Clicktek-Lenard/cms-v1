<?php

namespace App\Models\cms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Receiving extends Model
{
	/**
	* The table associated with the model.
	*
	* @var string
	*/
    protected $connection = 'CMS';
	protected $table = 'Receiving';
	protected $primaryKey = 'Id';

    protected $fillable = [
        // 'Id',
        'IdQueue',
        'IdBUFrom',
        'IdBUTo',
        'ItemCode',
        'DateReceived',
        'ReceivedBy',
        'Notes',
        'Status'
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

}
