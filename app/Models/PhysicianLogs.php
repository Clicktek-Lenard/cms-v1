<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhysicianLogs extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'PhysicianLogs';
	protected $primaryKey = 'IdPhysician';
	/**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
