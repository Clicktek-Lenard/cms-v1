<?php

namespace App\Models\eros;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ItemMasterList extends Model
{
	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $connection = 'Eros';
	protected $table = 'ItemMaster';
	protected $primaryKey = 'Id';
	protected $fillable = ['Group', 'SubGroup', 'NewCode', 'Code', 'Description', 'OrderStatus', 'ItemStatus', 'LongName', 'Side', 'Type', 'LISCode', 'WebSiteDescription'];
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
	
	public static function getItem($status = array() )
	{
		if(empty($status)) return "Missing Param!";
		return DB::connection('Eros')->table('ItemCode')->whereIn('Status', $status);
	}
	
	public static function createItemMasterEntry(array $data)
    {
        // Use Eloquent to create a new entry in 'ItemMaster'
        return self::create($data);
    }
	
}
