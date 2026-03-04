<?php

namespace App\Models\cms;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Company extends Model
{

	/**
     * Searchable rules.
     *
     * @var array
     */
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Companies';
	protected $primaryKey = 'Id';
	/**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
	/**
     * Relationship Table CompanyCategory.
     *
     * @var array
     */
	public function category()
	{
		return $this->hasOne('App\Models\CompanyCategory', 'Id', 'IdCategory');
	}
	
	public function getComanyList($params = array())
	{
		if(!empty($params['status']) )
		{
			return Company::where('Status', $params['status'] );
		}
		else if(!empty($params['Id']))
		{
			return Company::where('Id', $params['Id'] );
		}
		else if(!empty($params['All']))
		{
			return Company::select('*');
		}
	}
	public function postUpdate($request,$id)
	{
		return Company::where('Id',$id)
				->lockForUpdate()
				->update([
					'Code'		=> strtoupper(Str::of($request->input('Code'))->replaceMatches('/ {2,}/', ' '))
					,'Name'		=> strtoupper(Str::of($request->input('Name'))->replaceMatches('/ {2,}/', ' '))
					,'Status'		=>  $request->input('Status') 
				]); 
	}
	public function postInsert($request)
	{
		return Company::insertGetId([
				'Code'		=> strtoupper(Str::of($request->input('Code'))->replaceMatches('/ {2,}/', ' '))
				,'Name'		=> strtoupper(Str::of($request->input('Name'))->replaceMatches('/ {2,}/', ' '))
				,'Status'		=>  $request->input('Status')
				,'StartDate'	=> date('Y-m-d')
				,'EndDate'		=> date('Y-m-d')
		]);
	}
}
