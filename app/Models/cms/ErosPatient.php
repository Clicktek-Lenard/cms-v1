<?php

namespace App\Models\cms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ErosPatient extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    use HasFactory;
    protected $connection = 'Eros';
    protected $table = 'Patient';
    protected $primaryKey = 'Id';
    
    protected $fillable = [
        'Code',
        'FullName',
        'LastName',
        'FirstName',
        'MiddleName',
        'Suffix',
        'Prefix',
        'Gender',
        'DOB',
        'Email',
        'Email',
        'FullAddress',
        'Address',
        'Barangay',
        'BarangayName',
        'City',
        'CityName',
        'State',
        'ZipCode',
        'Nationality',
        'Country',
        'Religion',
        'ContactNo',
        'Moblie',
        'FaxNo',
        'PhilHealth',
        'SeniorId',
        'Remarks',
        'PictureLink',
        'UploadID',
        'InputDate',
        'InputBy',
        'UpdateDate',
        'UpdateBy',
        'LastVisit',
        'PassPortNo',
        'RDOB',
        'UploadDateTime',
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
