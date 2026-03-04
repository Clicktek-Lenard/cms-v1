<?php

namespace App\Models\cms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class QueueDisplay extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    use HasFactory;
    protected $connection = 'Queuing';
    protected $table = 'QueueDisplay';
    protected $primaryKey = 'Id';
    
    protected $fillable = [
        'FileName',
        'PictureLink',
        'Status',
        'StartDate',
        'EndDate',
        'Notes',
        'UploadDate',
        'UploadBy',
        'UpdateDate',
        'UpdateBy'
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
