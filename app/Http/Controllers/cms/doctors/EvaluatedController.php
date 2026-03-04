<?php

namespace App\Http\Controllers\cms\doctors;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\eros\ErosDB;
use Carbon\Carbon;
use DataTables;

class EvaluatedController extends Controller
{
	
	public function index()
	{
		$lCompa = DB::connection('Eros')->table('Company')->where('ResultUploading', 'LIKE', 'Yes')->get(array('Id', 'ErosCode', 'Name'));

		$sid = (isset($_GET['sid']))? $_GET['sid'] : 'ALL';

		return view('cms.doctor.evaluated',  ['fCompany' =>$lCompa, 'sid' => $sid  ]);
	}
	
	public function getCompanyList(Request $request)
	{
		//die('dddd');
		$search_arr = $request->get('search');
		$searchValue = $search_arr['value'];

		$sid = $request->get('sid');

		if ($request->ajax()) {
			$model = $this->getCompany(array('fullname'=>$searchValue));
			return DataTables::of($model)->toJson();
		}

	}
	
	public  function getCompany($params = array())
	{
		$lCompa = DB::connection('Eros')->table('Company')
			->where(function($q) use ($params ) {
				$q->where('ResultUploading', 'LIKE', 'Yes');
				if( !empty($params['sid']) && $params['sid'] != "ALL" )
				{
					$q->where('Id', '=', $params['sid'] );
				}
			})
			->get(array('Id','Code','Name','ErosCode','ResultUploading'));

		$return =  array();
		
		foreach($lCompa as $data)
		{
			array_push($return, array(
				'Id'			=> $data->Id
				,'Code'		=> $data->Code
				,'Name'		=> $data->Name
				,'ErosCode'	=> $data->ErosCode
				,'ResultUploading' => $data->ResultUploading
			));
		
		}
		
		return $return;	

	}


}