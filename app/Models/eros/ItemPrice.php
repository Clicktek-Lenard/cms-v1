<?php

namespace App\Models\eros;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ItemPrice extends Model
{
	/**
	* The table associated with the model.
	*
	* @var string
	*/
	protected $table = 'ItemPrice';
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
	public static function package()
	{
		return $this->hasMany('App\Models\Package', 'Id', 'ItemPriceId');
	}
	
	public static function getItemByCompany($params = array())
	{
		return  DB::connection('Eros')->table('ItemPrice')
		     ->select('Id')
		     ->where('CompanyCode', 'LIKE', $params['CompanyCode'])
		     ->where('Code', 'LIKE', $params['Code'])
		    // ->where('ClinicCode', 'LIKE', $params['ClinicCode'])
		     ->get();
	
	}
	public static function insertItemPrice($datas = array())
	{
		//local
		$data = 
		[
		    [
			'ClinicCode'	=> 'ALL',
			'Code'		=> $datas['Code'],
			'Description'	=> $datas['Description'],
			//'DescriptionEros'=> $datas['FirstName'],
			'CompanyCode'	=> $datas['CompanyCode'],
			'Price'		=> $datas['Price'],
			'PriceGroup'	=> $datas['PriceGroup'],
			'InputDate'	=> $datas['InputDate'],
			'InputBy'		=> $datas['InputBy']
		    ]
		];
		
		DB::connection('Eros')->table('ItemPrice')->insert($data);
	}
	
	public static function updateItemPrice($datas = array() )
	{
		DB::connection('Eros')->update("UPDATE `ItemPrice` SET `Price` = '".$datas['Price']."', `ErosStatus` = '".$datas['ErosStatus']."' , `CebuStatus` = '".$datas['CebuStatus']."' , `UpdateBy` = '".$datas['InputBy']."', `UpdateDate` = '".$datas['InputDate']."'
		WHERE  `Id` = '".$datas['Id']."'  ");
	}
	
}
