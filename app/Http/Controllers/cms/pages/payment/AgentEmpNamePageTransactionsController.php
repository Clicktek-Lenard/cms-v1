<?php

namespace App\Http\Controllers\cms\pages\payment;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\eros\AgentEmpName;

class AgentEmpNamePageTransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
	
	return AgentEmpName::AgentName();
	
    }
}    