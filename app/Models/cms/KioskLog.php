<?php

namespace App\Models\cms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class KioskLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    use HasFactory;
    protected $connection = 'Queuing';
    protected $table = 'Logs';
    protected $primaryKey = 'Id';
    
    protected $fillable = [
        'KioskId',
        'IdPatient',
        'ErosPatientId',
        'QueueNo',
        'DateTime',
        'Action',
        'ActionBy',
        'Room'
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
