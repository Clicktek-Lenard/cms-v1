<?php

namespace App\Models\cms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class CardEnrollment extends Model
{

    public static function registrationData()
	{

        return DB::connection('Eros')->table('CardEnrollment')
                ->where('Status', '=', '0')
                ->get(array('*'));
	}
        
    public static function receivingData()
	{

        $userClinicCode = session('userClinicCode'); // Get the userClinicCode from the session
       
        return DB::connection('Eros')->table('CardEnrollment')
                ->where('Status', '=', '0')
                ->where('ReleaseTo', '=', $userClinicCode) // Add this condition to filter by ReleaseBy
                ->get(array('*'));
	}

    public static function receivedData()
    {

        $userClinicCode = session('userClinicCode'); // Get the userClinicCode from the session

        return DB::connection('Eros')->table('CardEnrollment')
                ->where('Status', '=', '1')
                ->where('ReleaseTo', '=', $userClinicCode) // Add this condition to filter by ReleaseBy
                ->get(array('*'));
        }
    

    public static function getInfo($id = NULL)
	{
		
		$query = DB::connection('Eros')->table('CardEnrollment');
        
        if (!empty($id))
        {
            $query->where('Id', $id);
        }

        $data = $query->get();

        return $data;
        
	}

    public static function getUsers($id = NULL)
	{
		
		$query = DB::connection('Eros')->table('BusinessUnits');
        
        if (!empty($id))
        {
            $query->where('Id', $id);
        }

        $data = $query->get();

        return $data;
        
	}
    
} 
