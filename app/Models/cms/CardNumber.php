<?php

namespace App\Models\cms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class CardNumber extends Model
{
    protected $table = 'CardKey';
    protected $connection = 'Eros'; 
    protected $primaryKey = 'Id';
    protected $fillable = [
        'Year', 
        'Batch', 
        'Month', 
        'SeriesNum',
        'MaskedSeries',
        'GeneratedCardNumber'
    ];
    public $timestamps = false; 
}