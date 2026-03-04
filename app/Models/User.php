<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $primaryKey = 'id';
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
       // 'name',
      //  'email',
       // 'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
      //  'email_verified_at' => 'datetime',
    ];
    
    public static function getRoleModule()
    {
	//explode user role
	$exRole = explode("\n", Auth::user()->role);
	$iFind = "";
	foreach($exRole as $role)
	{
		$iFind .= "'$role',";
	}
	//DB::connection('mysql')->select  preg_replace('/\s+/', '_', $journalName);
	return json_encode(DB::connection('mysql')->select("SELECT * FROM `role` WHERE `ldap_role` IN (".str_replace(",'',","", preg_replace('/\s+/', '',$iFind)).") "));
    }
   public static function getBUDefault($code = null) 
   {
	if( !empty($code) )
	{
		return DB::connection('Eros')->table('BusinessUnits')
			->leftJoin('Company', 'BusinessUnits.DefaultPrice', '=', 'Company.ErosCode')
			->where('BusinessUnits.Code', $code);
	}
	return "Code shoud not be empty";
   
   }
   
   public static function getDepartmentCode($departmentName)
   {
	return DB::connection('mysql')->table('department')->where('Name', 'like', $departmentName)->get(array('Code'))[0]->Code;
   }
    
    
}
