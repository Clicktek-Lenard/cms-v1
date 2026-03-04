<?php

namespace App\Models\cms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Verification extends Model
{
    protected $table = 'CardVerified';
    protected $connection = 'Eros'; 
    protected $primaryKey = 'Id';
    // public static function verifyNumber()
    // {
    //     return DB::connection('Eros')->table('CardVerified')
    //         ->select('CardKey.GeneratedCardNumber as CardNumber', 'CardVerified.ICTReceived', 'CardVerified.DateReceived')
    //         ->leftJoin('CardVerified', 'CardKey.GeneratedCardNumber', '=', 'CardVerified.VerifiedCardNumbers');
    // }

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

    public static function getInfo()
    {
        $query = DB::connection('Eros')->table('CardVerified')
            ->select('CardVerified.*', 'CardKey.Year', 'CardKey.Batch', 'CardKey.Month')
            ->leftJoin('CardKey', 'CardVerified.VerifiedCardNumbers', '=', 'CardKey.GeneratedCardNumber');
        
        if (!empty($id)) {
            $query->where('CardVerified.Id', $id);
        }
    
        $data = $query->get();
    
        return $data;
    }
    public static function ItemUsed($id = null)
    {
        $query = DB::connection('Eros')->table('ItemPrice')
                    ->where('Id', $id)->get();
         
      
    
        return $query;
    } 
}

