<?php

namespace App\Models\cms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Sendout extends Model
{
 
    // protected $fillable = ['add_test', 'remove_test', 'edit_results_b_release'];
    // // or
    // protected $guarded = [];
	/**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
	/**
     * Fixed display all current date.
     *
     * @var array Used
     */
	public static function Sendout()
	{
		return DB::connection('CMS')->table('SendOut');
        
	}
}    