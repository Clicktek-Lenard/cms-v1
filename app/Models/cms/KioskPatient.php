<?php

namespace App\Models\cms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class KioskPatient extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    use HasFactory;
    protected $connection = 'Queuing';
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
        'SMS',
        'KCode',

        // 'Birthdate',
        // 'Gender',
        // 'Age',
        // 'Prefix',
        // 'Suffix',
        // 'PassportNum',
        // 'SeniorNum',
        // 'PwdNum',
        // 'expdatePwd',
        // 'Country',
        // 'Province',
        // 'City',
        // 'Municipality',
        // 'Street',
        // 'Zip',
        // 'Email',
        // 'Telephone',
        // 'Mobile',
        // 'PhotoPath',
        // 'Sms',
        // 'Code',

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
    public function getData($status = NULL)
	{
		 return DB::connection('Kiosk')->table('Patient');
	
	}
	
    public function province()
    {
        return $this->belongsTo(province::class, 'Province', 'province_id');
    }
    public function city()
    {
        return $this->belongsTo(city::class, 'City', 'city_id');
    }
    
   

}
