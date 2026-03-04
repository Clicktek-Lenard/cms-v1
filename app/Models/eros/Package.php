<?php

namespace App\Models\eros;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'Package';
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
	public function itemprice()
	{
		return $this->hasOne('App\Models\ItemPrice', 'ItemPriceId', 'Id');
	}
	
}
