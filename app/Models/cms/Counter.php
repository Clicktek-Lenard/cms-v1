<?php

namespace App\Models\cms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Counter extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    use HasFactory;
    protected $connection = 'Queuing';
    protected $table = 'Counter';
    protected $primaryKey = 'Id';
    
    protected $fillable = [ 
        'StationNumber',
        'IPv4',
        'Deparment',
        'IdBU'
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
